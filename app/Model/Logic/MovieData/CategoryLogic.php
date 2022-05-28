<?php
/**
 * Created by PhpStorm.
 * User: night
 * Date: 2021/5/26
 * Time: 15:59
 */

namespace App\Model\Logic\MovieData;


use App\Model\Entity\CollectionCategory;
use App\Model\Entity\MovieActorCategory;
use App\Model\Entity\MovieCategory;
use App\Model\Entity\MovieFilmCompaniesCategory;
use App\Model\Entity\MovieLabelCategory;
use App\Model\Entity\MovieSeriesCategory;
use Swoft\Log\Helper\CLog;

class CategoryLogic extends MovieDataBaseLogic
{
    private $type = [
        1,//影片
        2,//演员
        3,//标签
        4,//系列
        5,//片商
    ];

    private $typeName = [
        '有码',
        '欧美',
        '无码',
        'fc2',
        'Fc2',
        'fC2',
        'FC2',
    ];

    /**
     * 数据处理
     */
    public function dataRun()
    {
        $time = intval(config('WaitingDataTime',48));
        //$time = ($time<2||($time>(24*15)))?48:$time;
        $time = $time*60*60;
        $beginTime = time() - $time;
        
        $sql = '(status = 1 or status = 4)';
        $categoryData = CollectionCategory::where('created_at','>=',date('Y-m-d H:i:s',$beginTime))
            ->whereRaw($sql)->get();//类别数据不多一次可以全部处理
        foreach ($categoryData as $val)
        {
            $this->errorInfo->reset();//重置错误信息
            $this->dataDis($val);
            $status = 3;
            if(($this->errorInfo->code??500)!=200)
            {
                CLog::info('类别数据处理，同步类别数据出错！.错误说明：'.($this->errorInfo->msg??'未知错误'));
                //失败修改状态由人工处理
                $status = 5;//标记人工处理
            }
            //成功修改状态为已处理
            CollectionCategory::where('id',$val['id']??0)->update(
                [
                    'status'=>$status
                ]
            );
        }
    }

    /**
     * 指定数据处理
     * @param $data
     */
    public function dataDis($data)
    {
        if(($data['id']??0)<=0)
        {
            $this->errorInfo->setCode(500,'无效的类别数据！');
            return false;
        }

        $oid = ($data['id']??0);
        $type = ($data['type']??0);
        $name = ($data['name']??'');
        $status = ($data['status']??1);
        if (!in_array($type,$this->type))
        {
            $this->errorInfo->setCode(500,'无效的类别类型！');
            return false;
        }



        if($name == '')
        {
            $this->errorInfo->setCode(500,'无效的类别名称！');
            return false;
        }

        if (in_array($name,$this->typeName))
        {
            $this->errorInfo->setCode(200,'不需要处理的类别名称！');
            return true;
        }

        switch ($type)
        {
            case 1:
                {
                    $movieCategory = MovieCategory::where('name',$name)->firstArray();
                    $id = ($movieCategory['id']??0);
                    if($id<=0)
                    {
                        $movieCategoryObj = new MovieCategory();
                        $movieCategoryObj->setName($name);
                        $movieCategoryObj->setStatus(1);
                        $movieCategoryObj->setOid($oid);
                        $movieCategoryObj->save();
                        $id = $movieCategoryObj->getId();
                    }
                    else
                    {
                        if($status == 4)
                        {
                            MovieCategory::where('id',$id)->update([
                                'name'=>$name,
                                'status'=>1,
                                'oid'=>$oid
                            ]);
                        }
                    }
                    return $id;
                }
                break;
            case  2:
                {
                    $movieActorCategory = MovieActorCategory::where('name',$name)->firstArray();
                    $id = ($movieActorCategory['id']??0);
                    if($id<=0)
                    {
                        $movieActorCategoryObj = new MovieActorCategory();
                        $movieActorCategoryObj->setName($name);
                        $movieActorCategoryObj->setStatus(1);
                        $movieActorCategoryObj->setOid($oid);
                        $movieActorCategoryObj->save();
                        $id = $movieActorCategoryObj->getId();
                    }
                    else
                    {
                        if($status == 4)
                        {
                            MovieActorCategory::where('id',$id)->update([
                                'name'=>$name,
                                'status'=>1,
                                'oid'=>$oid
                            ]);
                        }
                    }
                    return $id;
                }
                break;
            case 3:
                {
                    $movieLabelCategory = MovieLabelCategory::where('name',$name)->firstArray();
                    $id = ($movieLabelCategory['id']??0);
                    if($id<=0)
                    {
                        $movieLabelCategoryObj = new MovieLabelCategory();
                        $movieLabelCategoryObj->setName($name);
                        $movieLabelCategoryObj->setStatus(1);
                        $movieLabelCategoryObj->setOid($oid);
                        $movieLabelCategoryObj->save();
                        $id = $movieLabelCategoryObj->getId();
                    }
                    else
                    {
                        if($status == 4)
                        {
                            MovieLabelCategory::where('id',$id)->update([
                                'name'=>$name,
                                'status'=>1,
                                'oid'=>$oid
                            ]);
                        }
                    }
                    return $id;
                }
                break;
            case 4:
                {
                    $movieSeriesCategory = MovieSeriesCategory::where('name',$name)->firstArray();
                    $id = ($movieSeriesCategory['id']??0);
                    if($id <= 0)
                    {
                        $movieSeriesCategoryObj = new MovieSeriesCategory();
                        $movieSeriesCategoryObj->setName($name);
                        $movieSeriesCategoryObj->setStatus(1);
                        $movieSeriesCategoryObj->setOid($oid);
                        $movieSeriesCategoryObj->save();
                        $id = $movieSeriesCategoryObj->getId();
                    }
                    else
                    {
                        if($status == 4)
                        {
                            MovieSeriesCategory::where('id',$id)->update([
                                'name'=>$name,
                                'status'=>1,
                                'oid'=>$oid
                            ]);
                        }
                    }
                    return $id;
                }
                break;

            case 5:
                {
                    $movieSeriesCategory = MovieFilmCompaniesCategory::where('name',$name)->firstArray();
                    $id = ($movieSeriesCategory['id']??0);
                    if($id <= 0)
                    {
                        $movieFilmCompaniesCategoryObj = new MovieFilmCompaniesCategory();
                        $movieFilmCompaniesCategoryObj->setName($name);
                        $movieFilmCompaniesCategoryObj->setStatus(1);
                        $movieFilmCompaniesCategoryObj->setOid($oid);
                        $movieFilmCompaniesCategoryObj->save();
                        $id = $movieFilmCompaniesCategoryObj->getId();
                    }
                    else
                    {
                        if($status == 4)
                        {
                            MovieFilmCompaniesCategory::where('id',$id)->update([
                                'name'=>$name,
                                'status'=>1,
                                'oid'=>$oid
                            ]);
                        }
                    }
                    return $id;
                }
                break;
        }

        $this->errorInfo->setCode(500,'未知错误类型！');
        return false;

    }
}