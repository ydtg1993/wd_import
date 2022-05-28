<?php
/**
 * Created by PhpStorm.
 * User: night
 * Date: 2021/5/19
 * Time: 15:51
 */

namespace App\Model\Logic\DataProcessing;


use App\Http\Common\Common;
use App\Http\Common\ErrorInfo;
use App\Model\Entity\CollectionActor;
use App\Model\Entity\CollectionActorName;
use App\Model\Entity\CollectionCategory;
use App\Model\Entity\CollectionComments;
use App\Model\Entity\CollectionDirector;
use App\Model\Entity\CollectionFilmCompanies;
use App\Model\Entity\CollectionLabel;
use App\Model\Entity\CollectionMovie;
use App\Model\Entity\CollectionNumber;
use App\Model\Entity\CollectionOriginal;
use App\Model\Entity\CollectionScore;
use App\Model\Entity\CollectionSeries;
use App\Model\Entity\Movie;
use App\Model\Logic\BaseLogic;
use Swoft\Log\Helper\CLog;
use Swoft\Redis\Redis;

class DataProcessingBaseLogic extends BaseLogic
{
    protected $errorTempInfo = null;//临时错误数据记录

    const RESOURCES_INFO_QUEUE = 'resources:info:queue'; //资源处理队列

    protected $type_dis = 1;//处理类型 1 是影片 2 是演员 3 标签   4.系列 5.


    /**
     * 解析数据来源的数据 将解析的数据进行格式化处理并保存到数据中
     * @param $data 要解析的数据
     * @param int $id 数据存在的ID
     */
    public function resolveHandle($data,$id = 0)
    {
        $number = $data['uid']??false;
        if($number === false)//没有番号的数据不予处理
        {
            $this->errorInfo->setCode(500,'无效的番号！');
            CLog::info('处理数据错误 原始数据ID：'.$id.'出现错误！错误说明：无效的番号！');
            return [];
        }
        $number = trim($number);

        //番号格式化
        $number = $this->numberDataHandle($data,$number);
        if($number === false)
        {
            $this->errorInfo->setCode(500,'番号异常！');
            //$this->errorTempInfo->setCode(500,'番号异常！');
            CLog::info('处理数据错误 原始数据ID：'.$id.'出现错误！错误说明：无效的番号！');
            return [];
        }

        //初始化临时错误对象
        $this->errorTempInfo = new ErrorInfo();

        //处理类别数据
        $reCategoryData = $this->categoryDataHandle($data,$id);
        if(($this->errorTempInfo->code??500) != 200)
        {
            CLog::info('处理采集类别数据番号：'.$number.' 原始数据ID：'.$id.'出现错误！错误说明：'.($this->errorTempInfo->msg??'未知错误'));
        }
        $this->errorTempInfo == null?($this->errorTempInfo = new ErrorInfo()):$this->errorTempInfo->reset();//重置错误信息

        //处理演员数据
        $reActorData = $this->actorDataHandle($data,$number,$id);
        if(($this->errorTempInfo->code??500) != 200)
        {
            CLog::info('处理采集演员数据番号：'.$number.' 原始数据ID：'.$id.'出现错误！错误说明：'.($this->errorTempInfo->msg??'未知错误'));
        }
        $this->errorTempInfo == null?($this->errorTempInfo = new ErrorInfo()):$this->errorTempInfo->reset();//重置错误信息

        //处理系列数据
        $reSeriesData = $this->seriesDataHandle($data,$id);
        if(($this->errorTempInfo->code??500) != 200)
        {
            CLog::info('处理采集系列数据番号：'.$number.' 原始数据ID：'.$id.'出现错误！错误说明：'.($this->errorTempInfo->msg??'未知错误'));
        }
        $this->errorTempInfo == null?($this->errorTempInfo = new ErrorInfo()):$this->errorTempInfo->reset();//重置错误信息

        //处理片商数据
        $reFilmData = $this->filmDataHandle($data,$id);
        if(($this->errorTempInfo->code??500) != 200)
        {
            CLog::info('处理采集片商番号：'.$number.' 原始数据ID：'.$id.'出现错误！错误说明：'.($this->errorTempInfo->msg??'未知错误'));
        }
        $this->errorTempInfo == null?($this->errorTempInfo = new ErrorInfo()):$this->errorTempInfo->reset();//重置错误信息

        //处理导演数据
        $reDirectorData = $this->directorDataHandle($data,$id);
        if(($this->errorTempInfo->code??500) != 200)
        {
            CLog::info('处理采集导演数据番号：'.$number.' 原始数据ID：'.$id.'出现错误！错误说明：'.($this->errorTempInfo->msg??'未知错误'));
        }
        $this->errorTempInfo == null?($this->errorTempInfo = new ErrorInfo()):$this->errorTempInfo->reset();//重置错误信息

        //处理标签数据
        $reLabelData = $this->labelDataHandle($data,$id);
        if(($this->errorTempInfo->code??500) != 200)
        {
            CLog::info('处理采集标签数据番号：'.$number.' 原始数据ID：'.$id.'出现错误！错误说明：'.($this->errorTempInfo->msg??'未知错误'));
        }
        $this->errorTempInfo == null?($this->errorTempInfo = new ErrorInfo()):$this->errorTempInfo->reset();//重置错误信息

        //处理评论数据
        $reCommentData = $this->commentRatingDataHandle($data,$number,$id);
        if(($this->errorTempInfo->code??500) != 200)
        {
            CLog::info('处理采集评论数据番号：'.$number.' 原始数据ID：'.$id.'出现错误！错误说明：'.($this->errorTempInfo->msg??'未知错误'));
        }
        $this->errorTempInfo == null?($this->errorTempInfo = new ErrorInfo()):$this->errorTempInfo->reset();//重置错误信息


        //处理视频数据
        $reMovieData = $this->movieDataHandle($data,$number,$id);
        if(($this->errorTempInfo->code??500) != 200)
        {
            CLog::info('处理采集视频数据番号：'.$number.' 原始数据ID：'.$id.'出现错误！错误说明：'.($this->errorTempInfo->msg??'未知错误'));
            $this->errorInfo->setCode(500,'影片数据处理失败！说明：'.($this->errorTempInfo->msg??'未知错误'));
            $reData = array();
            return $reData;
        }
        $this->errorTempInfo == null?($this->errorTempInfo = new ErrorInfo()):$this->errorTempInfo->reset();//重置错误信息

        $reResourcesData = $this->resourcesDataHandle($data,$reMovieData,$id);
        if(($this->errorTempInfo->code??500) != 200)
        {
            CLog::info('处理采集资源数据番号：'.$number.' 原始数据ID：'.$id.'出现错误！错误说明：'.($this->errorTempInfo->msg??'未知错误'));
        }

        $reData = array();
        $reData['actorData'] = $reActorData;
        $reData['seriesData'] = $reSeriesData;
        $reData['categoryData'] = $reCategoryData;
        $reData['filmData'] = $reFilmData;
        $reData['directorData'] = $reDirectorData;
        $reData['labelData'] = $reLabelData;
        $reData['CommentData'] = $reCommentData;
        $reData['movieData'] = $reMovieData;
        $reData['resourcesData'] = $reResourcesData;
        return $reData;
    }

    /**
     * 更新源数据状态
     * @param int $id
     */
    public function updateOriginalData($id = 0)
    {
        CollectionOriginal::where('id',$id)->update(['status'=>2]);
    }

    /**
     * 番号数据处理
     * @param $data
     * @param $number
     */
    public function numberDataHandle($data,$number)
    {
        if($number == '')//没有番号的数据不予处理
        {
            return $number;
        }

        $newNumber = strtoupper($number);
        $newNumberTemp = strstr($newNumber,'FC2');
        if($newNumberTemp === false)
        {
            $group = $data['group']??null;
            if($group == null)
            {
                return $number;
            }

            $group = trim($group);
            if ($group == '' || $group != '有码')
            {
                return $number;
            }
            $newNumbers = explode('-',$newNumber);
            if(count($newNumbers) > 3)
            {
                //异常日志
                $msg = '异常！番号：'.$number.'数据：'.json_encode($data);
                $this->logInfo($msg);
                return false;
            }
            $newNumberStr =  preg_replace('|[0-9\.\-\/\#]+|','',$newNumber);
            if($newNumberStr == '')
            {
                return $number;
            }
            $newNumberStr = substr($newNumberStr,0,1);//取出第一个字母

            $newNumber = strstr($newNumber,$newNumberStr);
            $newNumberNum =  preg_replace('/\D/s','',$newNumber);
            if($newNumberNum == '')
            {
                $newNumberNumD = strpos($newNumber,'.');
                $newNumberNumG = strpos($newNumber,'-');
                if($newNumberNumD != false ||$newNumberNumG != false )
                {
                    $newNumberNumLen = ($newNumberNumD > $newNumberNumG?$newNumberNumG:$newNumberNumD);
                    $beginStr = substr($newNumber,0,$newNumberNumLen);
                    $endStr = substr($newNumber,strlen($beginStr));
                    $endStrTemp = substr($endStr,0,3);
                    $endStr = ($endStrTemp == '000')?substr($endStr,2):$endStr;
                    return strtoupper($beginStr).'-'.$endStr;
                }

                if($newNumberNumD != false )
                {
                    $newNumberNumLen = $newNumberNumD;
                    $beginStr = substr($newNumber,0,$newNumberNumLen);
                    $endStr = substr($newNumber,strlen($beginStr));
                    $endStrTemp = substr($endStr,0,3);
                    $endStr = ($endStrTemp == '000')?substr($endStr,2):$endStr;
                    return strtoupper($beginStr).'-'.$endStr;
                }

                if($newNumberNumG != false  )
                {
                    $newNumberNumLen = $newNumberNumG;
                    $beginStr = substr($newNumber,0,$newNumberNumLen);
                    $endStr = substr($newNumber,strlen($beginStr));
                    $endStrTemp = substr($endStr,0,3);
                    $endStr = ($endStrTemp == '000')?substr($endStr,2):$endStr;
                    return strtoupper($beginStr).'-'.$endStr;
                }

                return $number;
            }
            $newNumberNum = substr($newNumberNum,0,1);//取出第一个数字
            $newNumberNumLen = strpos($newNumber,$newNumberNum);
            $newNumberNumD = strpos($newNumber,'.');
            $newNumberNumG = strpos($newNumber,'-');

            if($newNumberNumD != false)
            {
                $newNumberNumLen = ($newNumberNumLen > $newNumberNumD?($newNumberNumD):$newNumberNumLen);
            }
            if($newNumberNumG != false)
            {
                $newNumberNumLen = ($newNumberNumLen > $newNumberNumG?($newNumberNumG):$newNumberNumLen);
            }

            $beginStr = substr($newNumber,0,$newNumberNumLen);
            $endStr = ($newNumberNumG == $newNumberNumLen ||$newNumberNumLen== $newNumberNumD)?substr($newNumber,strlen($beginStr)+1):substr($newNumber,strlen($beginStr));

            $endStrTemp = substr($endStr,0,3);
            $endStr = ($endStrTemp == '000')?substr($endStr,2):$endStr;
            return strtoupper($beginStr).'-'.$endStr;
        }

        $newNumber = substr($newNumberTemp,3);
        $newNumberTemp = strstr($number,'-');
        if($newNumberTemp === false)
        {
            $newNumber = preg_replace('|[a-zA-Z]+|','',$newNumber);
            return ('FC2-'.$newNumber);
        }
        $newNumber = substr($newNumberTemp,1);
        $newNumbers = explode('-',$newNumber);
        if(count($newNumbers) >0)
        {
            $newNumber = 'FC2';
            foreach ($newNumbers as $k=>$val)
            {
                $k==0?($val=preg_replace('|[a-zA-Z]+|','',$val)):null;
                $val==''?null:($newNumber = $newNumber.'-'.$val);
            }
        }
        else
        {
            $newNumber = 'FC2-'.preg_replace('|[a-zA-Z]+|','',$newNumber);

        }
        return $newNumber;
    }

    /**
     * 演员数据处理
     * @param $data
     * @return int
     */
    public function actorDataHandle($data,$number,$id)
    {
        $this->errorTempInfo == null?($this->errorTempInfo = new ErrorInfo()):null;
        //读取片商数据
        $actor = $data['actor']??false;
        if($actor === false)
        {
            $this->errorTempInfo->setCode(500,'没有演员');
            return [];
        }
        $reData = [];
        if(is_array($actor))
        {
            foreach ($actor as $value)
            {
                $actorName = '';
                $actorSex = false;
                if(is_array($value))
                {
                    $actorName = $value[0]??false;//名字
                    $actorSex = $value[1]??false;//性别
                    if($actorName == false)
                    {
                        CLog::info('处理数据番号：'.$number.' 原始数据ID：'.$id.'出现错误！错误说明：演员处理——无效的演员名称！');
                        continue;
                    }
                }
                else if(is_string($value))
                {
                    $actorName = $value;//名称
                    $actorSex = false;//没有性别
                }
                else
                {
                    CLog::info('处理数据番号：'.$number.' 原始数据ID：'.$id.'出现错误！错误说明：演员处理——无效的数据类型！');
                    continue;
                }

                $actorName = trim($actorName);
                $collectionActorName = CollectionActorName::where('name',$actorName)->firstArray();
                if(($collectionActorName['aid']??0)<=0)//不存在该演员
                {
                    //创建演员
                    $collectionActor =  new CollectionActor();
                    $collectionActor->setName($actorName);//关联名称冗余
                    if($actorSex!==false)
                    {
                        $collectionActor->setSex($actorSex);
                    }
                    $collectionActor->setSource(2);//标识来源未影片解析
                    $collectionActor->setResourcesStatus(2);//必须标识未已下载否则会导致另外一边 报异常
                    $collectionActor->setOriginalId($id);
                    $collectionActor->save();

                    $collectionActorObj =  new CollectionActorName();
                    $collectionActorObj->setName($actorName);
                    $collectionActorObj->setAid($collectionActor->getId());
                    $collectionActorObj->save();
                    $reData[] = $collectionActor->getId();
                }
                else
                {
                    //修改演员性别 如果性别不为''
                    if(($collectionActorName['status']??1)!=2)//人工处理过的不在处理
                    {
                        $updataArr = ['sex'=>$actorSex];
                        ($collectionActorName['status']??1)==3?$updataArr['status'] = 4:null;
                        if($actorSex !== false)
                        {
                            CollectionActor::where('id',($collectionActorName['aid']??0))->where('sex','')->update($updataArr);
                        }
                        $reData[] = ($collectionActorName['aid']??0);
                    }

                }
            }
        }
        else if (is_string($actor))
        {
            $actor = explode(',',$actor);
            foreach ($actor as $value)
            {
                $actorName = $value;//名称
                $actorName = trim($actorName);
                $collectionActorName = CollectionActorName::where('name',$actorName)->firstArray();
                $aid = ($collectionActorName['aid']??0);
                if($aid<=0)//不存在该演员
                {
                    //创建演员
                    $collectionActor =  new CollectionActor();
                    $collectionActor->setName($actorName);//关联名称-主名称冗余
                    $collectionActor->setSource(2);//标识来源未影片解析
                    $collectionActor->setOriginalId($id);
                    $collectionActor->setResourcesStatus(2);//必须标识未已下载否则会导致另外一边 报异常
                    $collectionActor->save();

                    $collectionActorObj =  new CollectionActorName();
                    $collectionActorObj->setName($actorName);
                    $collectionActorObj->setAid($collectionActor->getId());
                    $collectionActorObj->save();
                    $aid = $collectionActor->getId();
                }
                $reData[] = $aid;
            }
        }
        else
        {
            $this->errorTempInfo->setCode(500,'数据类型错误');
        }


        return $reData;
    }

    /**
     * 采集系列处理
     * @param $data
     * @return int|null
     */
    public function seriesDataHandle($data,$id)
    {
        $this->errorTempInfo == null?($this->errorTempInfo = new ErrorInfo()):null;
        //读取系列数据
        $series = $data['series']??false;
        $group = $data['group']??null;//由于系列没有单独采集所以他的类别需要直接从影片分配
        if($series === false)
        {
            $this->errorTempInfo->setCode(500,'没有系列');
            return 0;
        }
        $series = trim($series);
        if($group != null)
        {
            $group = trim($group);
            if($group != '')
            {
                $collectionCategory = CollectionCategory::where('name',$group)->where('type',4)->firstArray();
                if(($collectionCategory['id']??0)<=0)//没有该类别
                {
                    //创建类别
                    $collectionCategory = new CollectionCategory();
                    $collectionCategory->setName($group);
                    $collectionCategory->setType(4);
                    $collectionCategory->setOriginalId($id);
                    $collectionCategory->save();
                    //return $collectionCategory->getId();
                }
            }

        }

        $collectionSeries = CollectionSeries::where('name',$series)->firstArray();
        if(($collectionSeries['id']??0)<=0)//没有该系列
        {
            //创建系列
            $collectionSeriesDb = new CollectionSeries();
            $collectionSeriesDb->setName($series);
            $collectionSeriesDb->setOriginalId($id);
            $collectionSeriesDb->setCategory($group);
            $collectionSeriesDb->save();
            return $collectionSeriesDb->getId();
        }
        return ($collectionSeries['id']??0);
    }

    /**
     * 片商数据处理
     * @param $data
     */
    public function filmDataHandle($data,$id)
    {
        $this->errorTempInfo == null?($this->errorTempInfo = new ErrorInfo()):null;
        //读取片商数据
        $producer = $data['producer']??false;
        $group = $data['group']??null;//由于片商没有单独采集所以他的类别需要直接从影片分配
        if($producer === false)
        {
            $this->errorTempInfo->setCode(500,'没有片商');
            return 0;
        }

        $producer = trim($producer);
        if($group != null)
        {
            $group = trim($group);
            $collectionCategory = CollectionCategory::where('name',$group)->where('type',5)->firstArray();
            if(($collectionCategory['id']??0)<=0)//没有该类别
            {
                //创建类别
                $collectionCategory = new CollectionCategory();
                $collectionCategory->setName($group);
                $collectionCategory->setType(5);
                $collectionCategory->setOriginalId($id);
                $collectionCategory->save();
                return $collectionCategory->getId();
            }
        }

        $collectionFilm = CollectionFilmCompanies::where('name',$producer)->firstArray();
        if(($collectionFilm['id']??0)<=0)//没有改片商
        {
            //创建片商
            $collectionFilmDb = new CollectionFilmCompanies();
            $collectionFilmDb->setName($producer);
            $collectionFilmDb->setOriginalId($id);
            $collectionFilmDb->setCategory($group);
            $collectionFilmDb->save();
            return $collectionFilmDb->getId();
        }

        return ($collectionFilm['id']??0);
    }

    /**
     * 采集导演处理
     * @param $data
     * @return int|null
     */
    public function directorDataHandle($data,$id)
    {
        $this->errorTempInfo == null?($this->errorTempInfo = new ErrorInfo()):null;
        //读取导演数据
        $direct = $data['direct']??false;
        if($direct === false)
        {
            $this->errorTempInfo->setCode(500,'没有导演');
            return 0;
        }

        $direct = trim($direct);
        $collectionDirector = CollectionDirector::where('name',$direct)->firstArray();
        if(($collectionDirector['id']??0)<=0)//没有该导演
        {
            //创建导演
            $collectionDirectorDb = new CollectionDirector();
            $collectionDirectorDb->setName($direct);
            $collectionDirectorDb->setOriginalId($id);
            $collectionDirectorDb->save();
            return $collectionDirectorDb->getId();
        }
        return ($collectionDirector['id']??0);
    }

    /**
     * 采集类别处理
     * @param $data
     * @return int|null
     */
    public function categoryDataHandle($data,$id)
    {
        $this->errorTempInfo == null?($this->errorTempInfo = new ErrorInfo()):null;
        //读取类别数据
        $group = $data['group']??null;
        if($group === null)
        {
            $this->errorTempInfo->setCode(500,'没有该类别');
            return 0;
        }
        $group = trim($group);
        if($group != null)
        {
            $collectionCategory = CollectionCategory::where('name',$group)->where('type',$this->type_dis)->firstArray();
            if(($collectionCategory['id']??0)<=0)//没有该类别
            {
                //创建类别
                $collectionCategory = new CollectionCategory();
                $collectionCategory->setName($group);
                $collectionCategory->setType($this->type_dis);
                $collectionCategory->setOriginalId($id);
                $collectionCategory->save();
                return $collectionCategory->getId();
            }
            return ($collectionCategory['id']??0);
        }
        return 0;
    }

    /**
     * 采集标签处理
     * @param $data
     * @return int|null
     */
    public function labelDataHandle($data,$id)
    {
        $this->errorTempInfo == null?($this->errorTempInfo = new ErrorInfo()):null;
        //读取导演数据
        $video_sort = $data['video_sort']??false;
        $group = $data['group']??false;
        if($video_sort === false)
        {
            $this->errorTempInfo->setCode(500,'没有标签');
            return [];
        }

        if(is_array($video_sort) )
        {
            $reData = [];
            if(count($video_sort) > 0)
            {
                foreach ($video_sort as $value)
                {
                    $value = trim($value);
                    if($group === false || $group == '')
                    {
                        $collectionLabel = CollectionLabel::where(['name'=>$value,['name_child','=',$value,'or'],['name_temp','=',$value,'or']])->whereNull('category')->firstArray();
                        if(($collectionLabel['id']??0)<=0)//没有该标签
                        {
                            //创建标签
                            $collectionLabelDb = new CollectionLabel();
                            $collectionLabelDb->setNameTemp($value);
                            $collectionLabelDb->setOriginalId($id);
                            $collectionLabelDb->save();
                            $reData[] = $collectionLabelDb->getId();
                        }
                    }
                    else
                    {
                        $group = trim($group);
                        $collectionCategoryTemp = CollectionCategory::where('name',$group)->where('type',3)->firstArray();
                        if(($collectionCategoryTemp['id']??0)<=0)
                        {
                            //创建类别
                            $collectionCategory = new CollectionCategory();
                            $collectionCategory->setName($group);
                            $collectionCategory->setType(3);
                            $collectionCategory->setOriginalId($id);
                            $collectionCategory->save();

                        }
                        $collectionLabel = CollectionLabel::where(['name'=>$value,['name_child','=',$value,'or'],['name_temp','=',$value,'or']])->where('category',$group)->firstArray();
                        if(($collectionLabel['id']??0)<=0)//没有该标签
                        {
                            //创建标签
                            $collectionLabelDb = new CollectionLabel();
                            $collectionLabelDb->setNameTemp($value);
                            $collectionLabelDb->setCategory($group);
                            $collectionLabelDb->setOriginalId($id);
                            $collectionLabelDb->save();
                            $reData[] = $collectionLabelDb->getId();
                        }
                    }

                }
            }
            else
            {
                $this->errorTempInfo->setCode(500,'没有标签');
                return [];
            }

            return $reData;
        }
        else if(is_string($video_sort))
        {
            $reData = [];
            if($video_sort != '')
            {
                $data = explode(',',$video_sort);
                foreach ($data as $value)
                {
                    $value = trim($value);
                    if ($group === false || $group == '')
                    {
                        $collectionLabel = CollectionLabel::where(['name'=>$value,['name_child','=',$value,'or'],['name_temp','=',$value,'or']])->whereNull('category')->firstArray();
                        if (($collectionLabel['id'] ?? 0) <= 0)//没有该标签
                        {
                            //创建标签
                            $collectionLabelDb = new CollectionLabel();
                            $collectionLabelDb->setNameTemp($value);
                            $collectionLabelDb->setOriginalId($id);
                            $collectionLabelDb->save();
                            $reData[] = $collectionLabelDb->getId();
                        }
                    }
                    else
                    {
                        $group = trim($group);
                        $collectionCategoryTemp = CollectionCategory::where('name',$group)->where('type',3)->firstArray();
                        if(($collectionCategoryTemp['id']??0)<=0)
                        {
                            //创建类别
                            $collectionCategory = new CollectionCategory();
                            $collectionCategory->setName($group);
                            $collectionCategory->setType(3);
                            $collectionCategory->setOriginalId($id);
                            $collectionCategory->save();

                        }

                        $collectionLabel = CollectionLabel::where(['name'=>$value,['name_child','=',$value,'or'],['name_temp','=',$value,'or']])->where('category',$group)->firstArray();
                        if(($collectionLabel['id']??0)<=0)//没有该标签
                        {
                            //创建标签
                            $collectionLabelDb = new CollectionLabel();
                            $collectionLabelDb->setNameTemp($value);
                            $collectionLabelDb->setCategory($group);
                            $collectionLabelDb->setOriginalId($id);
                            $collectionLabelDb->save();
                            $reData[] = $collectionLabelDb->getId();
                        }
                    }
                }
            }

            return $reData;
        }

        $this->errorTempInfo->setCode(500,'无效的标签类型!');
        return [];
    }

    /**
     * 采集评论频分处理
     * @param $data
     * @return int|null
     */
    public function commentRatingDataHandle($data,$number,$id)
    {
        $this->errorTempInfo == null?($this->errorTempInfo = new ErrorInfo()):null;
        //读取评论数据
        $comments = $data['comments']??false;
        if($comments === false)
        {
            $this->errorTempInfo->setCode(500,'没有评论及评分');
            return 0;
        }

        $reData = [];
        if(is_array($comments) && count($comments)>0)
        {
            foreach ($comments as $val)
            {
                $tempComments_id = 0;
                $tempScore_id = 0;
                $user_name = $val['commentator']??'';
                $content = $val['comment_text']??'';
                if(!is_string($content)){
                    $content = '';
                }
                $score = (($val['score']??null ) == null?null:$this->getScoreDis($val['score']??null));
                $content_time = Common::isDateTime($val['comment_time']??'');
                $collectionCommentsInfo = CollectionComments::where('user_name',$user_name)
                    ->where('number',$number)
                    ->where('original_id',$id)
                    ->where('content',$content)
                    ->where('content_time',$content_time)
                    ->firstArray();
                $tempComments_id = ($collectionCommentsInfo['id']??0);
                if($tempComments_id <= 0)
                {
                    $collectionCommentsObj = new CollectionComments();
                    $collectionCommentsObj->setNumber($number);
                    $collectionCommentsObj->setOriginalId($id);
                    $collectionCommentsObj->setContent($content);

                    $collectionCommentsObj->setUserName($user_name);
                    $collectionCommentsObj->setContentTime($content_time);
                    $collectionCommentsObj->setStatus(1);
                    $collectionCommentsObj->setScore($score);
                    $collectionCommentsObj->save();

                    $tempComments_id = $collectionCommentsObj->getId();
                }

                if($score != null)
                {
                    $collectionScoreInfo = CollectionScore::where('user_name',$user_name)
                        ->where('number',$number)
                        ->where('original_id',$id)
                        ->where('score',$score)
                        ->where('content_time',$content_time)
                        ->firstArray();
                    $tempScore_id = ($collectionScoreInfo['id']??0);
                    if($tempScore_id <= 0)
                    {
                        $collectionScoreObj = new CollectionScore();
                        $collectionScoreObj->setNumber($number);
                        $collectionScoreObj->setOriginalId($id);
                        $collectionScoreObj->setScore($score);

                        $collectionScoreObj->setUserName($user_name);
                        $collectionScoreObj->setContentTime($content_time);
                        $collectionScoreObj->setStatus(1);
                        $collectionScoreObj->save();

                        $tempScore_id = $collectionScoreObj->getId();
                    }
                }

                $reData[] = ['commentsId'=>$tempComments_id,'scoreId'=>$tempScore_id];
            }
        }

        return $reData;
    }

    //影片数据处理
    public function movieDataHandle($data,$number,$id)
    {
        //初始化返回数据
        $reData = [];
        $reData['is_dis'] = false;
        $reData['movieData'] = [];
        $numberTemp = $data['uid']??false;

        $this->errorTempInfo == null?($this->errorTempInfo = new ErrorInfo()):null;
        if($numberTemp === false)//没有番号的数据不予处理
        {
            $this->errorInfo->setCode(500,'无效的番号！');
            $this->errorTempInfo->setCode(500,'无效的番号！');
            CLog::info('处理数据错误 原始数据ID：'.$id.'出现错误！错误说明：无效的番号！');
            return [];
        }
        $numberTemp = trim($numberTemp);


        if($number === false)//没有番号的数据不予处理
        {
            $this->errorInfo->setCode(500,'无效的番号！');
            $this->errorTempInfo->setCode(500,'无效的番号！');
            CLog::info('处理数据错误 原始数据ID：'.$id.'出现错误！错误说明：无效的番号！');
            return $reData;
        }

        //影片番号处理
        $arrName = '';
        $arrNumber = explode('.',$number);
        if(is_array($arrNumber) && count($arrNumber) > 1 )
        {
            $arrName = $arrNumber[0];
        }
        else
        {
            $arrNumber = explode('-',$number);
            if(is_array($arrNumber) && count($arrNumber) > 1 )
            {
                $arrName = $arrNumber[0];
            }
            else
            {
                $arrNumber = explode('_',$number);
                if(is_array($arrNumber) && count($arrNumber) > 1 )
                {
                    $arrName = $arrNumber[0];
                }
                else
                {
                    //暂时没发现其他规则
                }
            }
        }
        $arrName = trim($arrName);
        if($arrName != '')
        {
            $collectionNumber = CollectionNumber::where('name',$arrName)->firstArray();
            if(($collectionNumber['id']??0)<=0)//没有该番号
            {
                //没有该番号
                $collectionNumberObj = new CollectionNumber();
                $collectionNumberObj->setName($arrName);
                $collectionNumberObj->save();
            }
        }


        $collectionMovieData = CollectionMovie::where('number',$number)->get();
        $is_abnormal_data = false;
        if(count($collectionMovieData) > 0)
        {
            //异常数据
            $is_abnormal_data = true;//标记为异常数据 如果没有异常就直接退出了所有不用切回状态
            foreach ($collectionMovieData as $val)
            {
                if(($val['id']??0)>0)
                {
                    if(($val['status']??1) == 2)
                    {
                        //已经人工处理过的 不做处理
                        $reData['movieData'] = $val;
                        return $reData;
                    }

                    if(strtotime(($val['ctime']??'')) == strtotime(($data['ctime']??'')) && ($val['source_site']??'') == ($data['domain_name']??''))
                    {
                        //相同数据不做处理
                        $reData['movieData'] = $val;
                        return $reData;
                    }
                }
            }
        }

        //影片组图大小图处理
        $preview_img = $data['preview_img']??[];
        $preview_big_img = $data['preview_big_img']??[];
        $mapInfo = [];
        foreach ($preview_img as $k=>$val)
        {
            $mapInfo[] = ['img'=>($preview_img[$k]??''),'big_img'=>($preview_big_img[$k]??'')];
        }

        //磁链书数据处理
        $magnet = $data['magnet']??[];
        $reDataMagnet = [];
        foreach ($magnet as $value)
        {
            $temp =  $this->fluxLinkageFormat($value);
            $reDataMagnet[] = $temp;
        }


        //播放时长处理
        $video_time = $data['video_time']??false;
        $video_time = $video_time == false?null:$this->getVideoTime($video_time);
        $video_title = $data['video_title']??'';
        $video_title = trim($video_title);
        $video_title = strlen($video_title)>=700?mb_substr($video_title,0,700):$video_title;
        $score_video = (($data['score']??null ) == null?null:$this->getScoreDis($data['score']??null));

        $collectionMovieDb = new CollectionMovie();
        $collectionMovieDb->setNumber($number);
        $collectionMovieDb->setNumberSource($numberTemp);
        $collectionMovieDb->setNumberName($arrName);
        $collectionMovieDb->setName($video_title);
        $collectionMovieDb->setSourceSite(trim($data['domain_name']??''));
        $collectionMovieDb->setSourceUrl(trim($data['video_url']??''));

        $collectionMovieDb->setDirector(trim($data['direct']??''));
        $collectionMovieDb->setSell(trim($data['sell']??''));
        $collectionMovieDb->setTime(intval($video_time) );
        $collectionMovieDb->setReleaseTime( Common::isDateTime($data['release_time']??''));

        $collectionMovieDb->setSmallCover(trim($data['small_cover']??''));
        $collectionMovieDb->setBigCove(trim($data['big_cover']??''));
        $collectionMovieDb->setTrailer(trim($data['trailer']??''));
        $collectionMovieDb->setMap(json_encode($mapInfo));

        $collectionMovieDb->setSeries(trim($data['series']??''));
        $collectionMovieDb->setFilmCompanies(trim($data['producer']??''));
        $collectionMovieDb->setIssued(trim($data['publisher']??''));
        $collectionMovieDb->setActor(json_encode($data['actor']??[]));

        $collectionMovieDb->setCategory(trim($data['group']??''));
        $collectionMovieDb->setLabel(json_encode($data['video_sort']??[]));
        $collectionMovieDb->setScore( $score_video);
        $collectionMovieDb->setScorePeople( intval($data['score_man']??0));

        $collectionMovieDb->setCommentNum(count($data['comments']??[]));
        $collectionMovieDb->setComment(json_encode($data['comments']??[]));
        $collectionMovieDb->setActualSource(trim($data['actual_source']??''));
        $collectionMovieDb->setFluxLinkageNum(count($reDataMagnet));

        $collectionMovieDb->setFluxLinkage(json_encode($reDataMagnet));
        $collectionMovieDb->setIsDownload( ((($data['is-success']??1)==1)?1:2) );
        $collectionMovieDb->setIsSubtitle( ((($data['is-warning']??1)==1)?1:2)  );
        $collectionMovieDb->setIsNew( ((($data['is-info']??2)==2)?2:1) );

        $collectionMovieDb->setStatus($is_abnormal_data?5:1);
        $collectionMovieDb->setCtime($data['ctime']??null);
        $collectionMovieDb->setUtime($data['utime']??null);
        $collectionMovieDb->setOriginalId($id);
        $collectionMovieDb->save();

        if($is_abnormal_data)
        {
            //异常数据
            $abnormal_data_ids = CollectionMovie::where('number',$number)->get()->pluck('id')->toArray();
            if(count($abnormal_data_ids) > 0)//将所有相同番号的数据处理修改为异常
            {
                $tempUpdata = [
                    'status'=>5,
                    'abnormal_data_id'=>json_encode($abnormal_data_ids)
                ];
                CollectionMovie::where('number',$number)->update($tempUpdata);
            }
            $collectionMovieDb->setAbnormalDataId(json_encode($abnormal_data_ids));
        }

        $reData['movieData'] = $collectionMovieDb->toArray();
        $reData['is_dis'] = true;
        return $reData;
    }

    //资源数据处理
    public function resourcesDataHandle($data,$movieData,$id)
    {
        $this->errorTempInfo == null?($this->errorTempInfo = new ErrorInfo()):null;
        //
        if(!($movieData['is_dis']??false))
        {
            return true;
        }
        $movieInfo = $movieData['movieData']??[];
        if(($movieInfo['id']??0) <= 0)
        {
            $this->errorTempInfo->setCode(500,'无效的番号！');
            CLog::info('处理数据错误 原始数据ID：'.$id.'出现错误！错误说明：无效的导入信息！——资源处理');
            return false;
        }

        //拼接Url
        $CollectResourcesUrl = config('CollectResourcesUrl');

        //数据处理
        $small_cover = $data['small_cover']??'';//小封面
        $big_cove = $data['big_cover']??'';//大封面
        $preview_img = $data['preview_img']??[];//其他图片
        $preview_big_img = $data['preview_big_img']??[];//其他图片-大
        $trailer = $data['trailer']??'';//预览视频

        $map = [];
        if(is_array($preview_img))
        {
            foreach ($preview_img as $k=>$value)
            {
                $big_img = $preview_big_img[$k]??'';
                $img = $CollectResourcesUrl.$value;
                $big_img = ($big_img==''?$img:($CollectResourcesUrl.$big_img));
                $map[] = ['img'=>$img,'big_img'=>$big_img];
            }
        }

        //组装数据
        $reData = [];
        $reData['mid'] = $movieInfo['id']??0;
        $reData['small_cover'] = ($small_cover=='')?$small_cover:($CollectResourcesUrl.$small_cover);//小封面
        $reData['big_cove'] = ($big_cove=='')?$big_cove:($CollectResourcesUrl.$big_cove);//大封面
        $reData['map'] = $map;//其他图片
        $reData['trailer'] = ($trailer=='')?$trailer:($CollectResourcesUrl.$trailer);//预览视频
        $reData['type'] = $data['db_name']??'';//资源处理类型
        self::addResourcesHandleQueue($reData);

        return true;
    }

    /**
     * 将数据添加进redis队列
     * @param array $data
     */
    public static  function  addResourcesHandleQueue($data = [])
    {
        if(is_array($data) && count($data) > 0)
        {
            Redis::lPush(self::RESOURCES_INFO_QUEUE,json_encode($data));
        }
    }

    /**
     * 获取一个队列数据
     * @return array
     */
    public static function getResourcesHandleQueue()
    {
        $data = Redis::rPop(self::RESOURCES_INFO_QUEUE);
        if($data)
        {
            return json_decode($data,true);
        }
        return null;
    }

    /**
     * 下载资源 并生成保存新的资源以及资源路径
     * @return bool
     */
    public function downResources($data = [])
    {
        $this->errorTempInfo = new ErrorInfo();
        if(($data['mid']??0) <= 0)
        {
            $this->errorInfo->setCode(500,'无效的资源处理数据！');
            return false;
        }

        $mid = $data['mid']??0;
        $small_cover = $data['small_cover']??'';
        $big_cove = $data['big_cove']??'';
        $map = $data['map']??[];
        $trailer = $data['trailer']??'';
        $type = $data['type']??'base';//资源处理类型

        $reData = [];
        $reData['mid'] = $mid;
        $reData['small_cover'] = '';
        $reData['big_cove'] = '';
        $reData['trailer'] = '';
        $reData['map'] = [];

        $oddInfo = [];
        $oddInfo['small_cover'] = '';
        $oddInfo['big_cove'] = '';
        $oddInfo['trailer'] = '';
        $oddInfo['map'] = [];
        $odd = false;
        if($small_cover != '')
        {
            $tempPath = $this->downFile($small_cover,$type,'small_cover');
            $reData['small_cover'] = ($tempPath != '')?$tempPath:'';
            if($this->errorTempInfo->code != 200)
            {
                $oddInfo['small_cover'] = $this->errorTempInfo->msg;
                $odd = true;
            }
        }

        if($big_cove != '')
        {
            $tempPath = $this->downFile($big_cove,$type,'big_cove');
            $reData['big_cove'] = ($tempPath != '')?$tempPath:'';
            if($this->errorTempInfo->code != 200)
            {
                $oddInfo['big_cove'] = $this->errorTempInfo->msg;
                $odd = true;
            }
        }

        if($trailer != '')
        {
            $tempPath = $this->downFile($trailer,$type,'trailer');
            $reData['trailer'] = ($tempPath != '')?$tempPath:'';
            if($this->errorTempInfo->code != 200)
            {
                $oddInfo['trailer'] = $this->errorTempInfo->msg;
                $odd = true;
            }
        }

        foreach ($map as $value)
        {
            if($value)
            {
                $tempPath = $this->downFile($value,$type,'map');
                $reData['map'][] = ($tempPath != '')?$tempPath:'';
                if($this->errorTempInfo->code != 200)
                {
                    $oddInfo['map'][] = $this->errorTempInfo->msg;
                    $odd = true;
                }
            }
        }

        $saveInfo =  ['resources_status'=>$odd?3:2,
            'resources_info'=>json_encode($reData),
            'resources_odd_info'=>json_encode($oddInfo)];

        //保存资源信息
        CollectionMovie::where('id',$mid)->update($saveInfo);
        return true;
    }

    /**
     * 获取字符串中的数字 只会获取第一个
     * @param $str
     * @return mixed|null
     */
    protected static function getStrNum($str)
    {
        $str = trim($str);
        if($str == 'N/A')
        {
            return null;
        }
        $r = [];
        preg_match('/(\d+)/',$str,$r);
        //preg_match_all("/\d+/",$str,$r);
        //return ($r[0]??[])[0]??null;
        return $r[1]??null;

    }

    /**
     * 下载文件
     * @param $url 需要下载的链接
     * @param $saveName 保存文件的全路径
     */

    protected  function downFile($url,$type = 'base',$fileDir = '')
    {
        $this->errorTempInfo = new ErrorInfo();
        $ext = pathinfo( parse_url( $url, PHP_URL_PATH ), PATHINFO_EXTENSION );//文件后缀
        $saveAllPath = config('DownResourcesSave');//保存全路径
        $savePath = '/'.$type.'/'.$fileDir;//保存路径
        Common::createDir($saveAllPath.$savePath);
        $saveFile = md5((time().$fileDir.$type.(rand(1000,9999)))).'.'.$ext;//保存文件名称
        $saveName = $saveAllPath.$savePath.'/'.$saveFile;
        try
        {
            // todo 本地屏蔽
            //由于下载PHP下载文件会导致内存爆满 所以关闭文件下载
            //$content = file_get_contents($url);
            //file_put_contents($saveName,$content);
            //unset($content);//下载完成要清除一下内存
            return $savePath.'/'.$saveFile;
        }catch (\Exception $e)
        {
            $this->errorTempInfo->setCode(500,'数据异常：'.$e->getMessage());
            return '';
        }
    }

    /**
     * 积分处理
     * @param $score
     */
    public function  getScoreDis($score)
    {
        return floatval($score)*2;
    }

    /**
     * 时长处理
     * @param $videoTime
     * @return |null
     */
    public function getVideoTime($videoTime)
    {
        $time = self::getStrNum($videoTime);//取出来是分钟
        return $time == null?null:(intval($time)*60);
    }

    public function logInfo($msg)
    {
        $saveAllPath = config('DownResourcesSave');//保存全路径
        $savePath = $saveAllPath.'/logError/'.(date('Ymd',time())).'.log';//保存路径
        echo date('Y-m-d H:i:s').' '.$msg.PHP_EOL;
        if(!is_file($savePath))
        {
            file_put_contents($savePath,date('Y-m-d H:i:s').' 创建日志!');
        }
        file_put_contents($savePath,PHP_EOL.date('Y-m-d H:i:s').' '.$msg,FILE_APPEND);
    }

    /**
     * 磁链数据处理
     */
    public function fluxLinkage($data)
    {
        if(($data['uid']??'') == '')
        {
            //没有番号的视为无效数据
            return false;
        }
        $magnet = $data['magnet']??array();
        $reData = [];
        $time = 0;
        foreach ($magnet as $value)
        {
            $temp =  $this->fluxLinkageFormat($value);
            $reData[] = $temp;
            $timeTemp  = strtotime($temp['time']);
            $time = (($timeTemp>$time)?$timeTemp:$time);
        }

        /*Movie::where('number_source',$data['uid']??'')->update([
            'flux_linkage_num'=>count($reData),
            'flux_linkage'=>json_encode($reData),
            'new_comment_time'=>date('Y-m-d H:i:s',($time == 0?time():$time)) ,
        ]);*/
        return true;
    }

    /**
     * 格式化磁链数据
     * @param $data
     */
    public function  fluxLinkageFormat($data)
    {
        $is_small = $data['is-small']??null;
        $is_small = $is_small=='' || $is_small == null ? 2:1;

        $is_warning = $data['is-warning']??null;
        $is_warning = $is_warning=='' || $is_warning == null ? 2:1;

        $tooltip = $data['tooltip']??null;
        $tooltip = $tooltip=='' || $tooltip == null ? 2:1;

        $magnet = $data['url']??null;
        $magnet = $magnet=='' || $magnet == null ? '':$magnet;
        $magnetTemp = strtr($magnet,[
            'javdb.com'=>'HDouban.com',
        ]);
        //var_dump($magnetTemp);

        $reData = [
            'name' => $data['name']??'',
            'url' => $magnetTemp,
            'meta' => $data['meta']??'',
            'is-small' => $is_small,
            'is-warning' => $is_warning,
            'tooltip' => $tooltip,
            'time' => Common::isDateTime($data['time']??'') ,
        ];
        return $reData;
    }



}
