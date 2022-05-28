<?php
/**
 * Created by PhpStorm.
 * User: night
 * Date: 2021/5/21
 * Time: 11:22
 */

namespace App\Model\Logic\DataProcessing;


use App\Http\Common\ErrorInfo;
use App\Model\Entity\CollectionActor;
use App\Model\Entity\CollectionActorName;
use App\Model\Entity\CollectionMovie;
use App\Model\Entity\CollectionOriginal;
use Swoft\Log\Helper\CLog;

class ActorLogic extends DataProcessingBaseLogic
{

    protected $type_dis = 2;//处理类型 1 是影片 2 是演员


    public function resolveHandle($data,$id = 0)
    {
        //初始化临时错误对象
        $this->errorTempInfo = new ErrorInfo();

        //处理类别数据
        $reCategoryData = $this->categoryDataHandle($data,$id);
        if(($this->errorTempInfo->code??500) != 200)
        {
            CLog::info('处理演员数据  原始数据ID：'.$id.'出现错误！错误说明：'.($this->errorTempInfo->msg??'未知错误'));
        }
        $this->errorTempInfo == null?($this->errorTempInfo = new ErrorInfo()):$this->errorTempInfo->reset();//重置错误信息

        //处理演员数据
        $reActorData = $this->actorDataHandle($data,0,$id);
        if(($this->errorTempInfo->code??500) != 200)
        {
            CLog::info('处理演员数据  原始数据ID：'.$id.'出现错误！错误说明：'.($this->errorTempInfo->msg??'未知错误'));
            $this->errorInfo->setCode(500,'影片数据处理失败！说明：'.($this->errorTempInfo->msg??'未知错误'));
            $reData = array();
            return $reData;
        }
        $this->errorTempInfo == null?($this->errorTempInfo = new ErrorInfo()):$this->errorTempInfo->reset();//重置错误信息

        //处理资源数据
        $reResourcesData = $this->resourcesDataHandle($data,$reActorData,$id);
        if(($this->errorTempInfo->code??500) != 200)
        {
            CLog::info('处理演员数据  原始数据ID：'.$id.'出现错误！错误说明：'.($this->errorTempInfo->msg??'未知错误'));
        }

        $reData = array();
        $reData['categoryData'] = $reCategoryData;
        $reData['actorData'] = $reActorData;
        $reData['resourcesData'] = $reResourcesData;
        return $reData;
    }

    /**
     * 演员数据处理
     * @param $data
     * @return int
     */
    public function actorDataHandle($data,$number,$id)
    {
        $this->errorTempInfo == null?($this->errorTempInfo = new ErrorInfo()):null;
        //初始化返回数据
        $reData = [];
        $reData['is_dis'] = false;
        $reData['actorData'] = [];

        //读取演员数据
        $actor = $data['actor_name']??false;
        if($actor === false)
        {
            $this->errorTempInfo->setCode(500,'无效的演员');
            return $reData;
        }

        //
        $actorName = explode(',',$actor);
        if(!(is_array($actorName) && count($actorName) >0 ))
        {
            $actorName = explode('，',$actor);//，有中文有英文 。。。
            if(!(is_array($actorName) && count($actorName) >0 ))
            {
                $this->errorTempInfo->setCode(500,'无效的演员');
                return $reData;
            }
        }

        $collectionActorName = null;//->firstArray()
        foreach ($actorName as $value)
        {
            $value = trim($value);
            if($collectionActorName == null)
            {
                $collectionActorName = CollectionActorName::where('name',$value);
            }
            else
            {
                $collectionActorName = $collectionActorName->orWhere('name',$value);
            }
        }

        $collectionActorData = $collectionActorName->firstArray();
        $aid = ($collectionActorData['aid']??0);
        if($aid > 0)
        {
            $collectionActor = CollectionActor::where('id',$aid)->firstArray();
            if(($collectionActor['status']??1) == 2)
            {
                //已经人工处理过的只增加新名称
            }
            else if(($collectionActor['original_id']??0) == $id)
            {
                //  已经处理过的
                $dataTempCollectionOriginal = CollectionOriginal::where('id',$id)->firstArray();
                if(($dataTempCollectionOriginal['status']??0) == 4)//需要重新处理
                {
                    $video_act = $data['act'] ?? false;
                    $video_act = $video_act == false ? null : self::getStrNum($video_act);
                    CollectionActor::where('id', $aid)
                        ->update([
                            'name' => trim($actorName[0] ?? ''),
                            'photo' => trim($data['avatar'] ?? ''),
                            'actual_source' =>trim( $data['actual_source'] ?? ''),
                            'source' =>1,

                            'movie_sum' => $video_act,
                            'category' => trim($data['group'] ?? ''),
                            'original_id' => $id,
                            'interflow' => json_encode($data['interflow'] ?? ''),

                            'social_accounts' => json_encode($data['interflow'] ?? ''),
                        ]);
                    $collectionActor = CollectionActor::where('id',$aid)->firstArray();
                }
            }
            $reData['actorData'] = $collectionActor;
        }
        else
        {
            $video_act = $data['act'] ?? false;
            $video_act = $video_act == false ? null : self::getStrNum($video_act);
            $actorNameTemp = $actorName[0]??'';
            $actorNameTemp = trim($actorNameTemp);
            $collectionActor =  new CollectionActor();
            $collectionActor->setName($actorNameTemp);//关联名称-主名称冗余
            $collectionActor->setSource(1);//标识来源为演员解析
            $collectionActor->setPhoto(trim($data['avatar'] ?? ''));

            $collectionActor->setActualSource(trim($data['actual_source'] ?? ''));
            $collectionActor->setMovieSum(intval($video_act));
            $collectionActor->setCategory(trim($data['group'] ?? ''));
            $collectionActor->setInterflow( json_encode($data['interflow'] ?? ''));

            $collectionActor->setSocialAccounts(json_encode($data['interflow'] ?? ''));
            $collectionActor->setOriginalId($id);
            $collectionActor->save();
            $aid = $collectionActor->getId();
            $reData['actorData'] = $collectionActor->toArray();
        }

        //更新名称
        foreach ($actorName as $value)
        {
            $value = trim($value);
            $collectionActorNameTemp = CollectionActorName::where('name',trim($value))->firstArray();
            if(($collectionActorNameTemp['aid']??0)<=0)
            {
                $collectionActorObj =  new CollectionActorName();
                $collectionActorObj->setName(trim($value));
                $collectionActorObj->setAid($aid);
                $collectionActorObj->save();
                CollectionActor::where('id',$aid)->update([
                    'status'=>4
                ]);//更新下 不然新增的名称不会同步进表
            }
        }
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
        $actorInfo = $movieData['actorData']??[];
        if(($actorInfo['id']??0) <= 0)
        {
            $this->errorTempInfo->setCode(500,'无效的演员数据！');
            CLog::info('处理数据错误 原始数据ID：'.$id.'出现错误！错误说明：无效的导入信息！——资源处理');
            return false;
        }

        //拼接Url
        $CollectResourcesUrl = config('CollectResourcesUrl');

        //数据处理
        $avatar = $data['avatar']??'';//头像

        //组装数据
        $reData = [];
        $reData['aid'] = $actorInfo['id']??0;
        if (filter_var($avatar, FILTER_VALIDATE_URL) !== false) {
            $reData['avatar'] = $avatar;
        }else{
            $reData['avatar'] = ($avatar=='')?$avatar:($CollectResourcesUrl.$avatar);//头像
        }

        $reData['type'] = $data['db_name']??'';//资源处理类型
        self::addResourcesHandleQueue($reData);
        return true;
    }

    /**
     * 下载资源 并生成保存新的资源以及资源路径
     * @return bool
     */
    public function downResources($data = [])
    {
        $this->errorTempInfo = new ErrorInfo();
        if(($data['aid']??0) <= 0)
        {
            $this->errorInfo->setCode(500,'无效的资源处理数据！');
            return false;
        }

        $aid = $data['aid']??0;
        $avatar = $data['avatar']??'';
        $avatar = trim($avatar);
        $type = $data['type']??'base';//资源处理类型

        $reData = [];
        $reData['aid'] = $aid;
        $reData['avatar'] = '';

        $oddInfo = [];
        $oddInfo['avatar'] = '';
        $odd = false;
        if($avatar != '')
        {
            $tempPath = $this->downFile($avatar,$type,'avatar');
            $reData['avatar'] = ($tempPath != '')?$tempPath:'';
            if($this->errorTempInfo->code != 200)
            {
                $oddInfo['avatar'] = $this->errorTempInfo->msg;
                $odd = true;
            }
        }
        else
        {
            $odd = true;//为空的不用下载直接可以用
        }

        $saveInfo =  ['resources_status'=>$odd?3:2,
            'resources_info'=>json_encode($reData),
            'resources_odd_info'=>json_encode($oddInfo)];

        //保存资源信息
        CollectionActor::where('id',$aid)->update($saveInfo);
        return true;
    }
}