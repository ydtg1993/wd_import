<?php
/**
 * Created by PhpStorm.
 * User: night
 * Date: 2021/5/26
 * Time: 9:41
 */

namespace App\Model\Logic\MovieData;


use App\Model\Entity\CollectionLabel;
use App\Model\Entity\MovieLabel;
use App\Model\Entity\MovieLabelCategory;
use App\Model\Entity\MovieLabelCategoryAssociate;
use Swoft\Log\Helper\CLog;

class LabelLogic extends MovieDataBaseLogic
{
    /**
     * 数据处理
     */
    public function dataRun()
    {
        $time = intval(config('WaitingDataTime',48));
        //$time = ($time<2||($time>(24*15)))?48:$time;
        $time = $time*60*60;
        $beginTime = time() - $time;

        $count = $categoryData = CollectionLabel::where('created_at','>=',date('Y-m-d H:i:s',$beginTime))->count();
        $disSum = intval($count);

        $tempIndex = 0;
        $pageIndex = 1;//翻页从第一页开始
        $disCount = 500;

        while ($tempIndex <= $disSum)
        {
            CLog::info('开始处理标签数据 第'.$pageIndex.'页数据！总共'.(ceil($disSum/$disCount)).'页!一次处理数据量：'.$disCount.'条');
            $handleData = CollectionLabel::where('created_at','>=',date('Y-m-d H:i:s',$beginTime))
                ->offset((($pageIndex - 1 ) * $disCount)<= 0 ? 0:(($pageIndex - 1 ) * $disCount))
                ->limit($disCount)->get();

            foreach ($handleData as $val)
            {
                $statusTemp = $val['status']??0;

                if(!($statusTemp == 1 || $statusTemp == 4 ))
                {
                    continue;
                }

                $this->errorInfo->reset();//重置错误信息
                $this->dataDis($val);
                $status = 3;
                if(($this->errorInfo->code??500)!=200)
                {
                    CLog::info('标签数据处理，同步标签数据出错！.错误说明：'.($this->errorInfo->msg??'未知错误'));
                    $status = 5;
                }

                //成功修改状态为已处理
                CollectionLabel::where('id',$val['id']??0)->update(
                    [
                        'status'=>$status
                    ]
                );

            }

            $tempIndex+=$disCount;
            $pageIndex++;
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
            $this->errorInfo->setCode(500,'无效的标签数据！');
            return false;
        }

        $oid = ($data['id']??0);
        $name = ($data['name']??'');
        $name_temp = ($data['name_temp']??'');
        $name_child = ($data['name_child']??'');
        $category = ($data['category']??'');
        $status = ($data['status']??1);

        if($name_temp != '')//如果临时标签名称不为空代表不存在 主名称以及子名称
        {
            if($category == '')
            {
                $movieLabel = MovieLabel::where('name',$name_temp)->firstArray();
                $id = ($movieLabel['id']??0);
                if($id <= 0)
                {
                    $movieLabelObj = new MovieLabel();
                    $movieLabelObj->setName($name_temp);
                    $movieLabelObj->setStatus(1);
                    $movieLabelObj->setOid($oid);
                    $movieLabelObj->save();
                    $id = $movieLabelObj->getId();
                }
                else
                {
                    if($status == 4)
                    {
                        MovieLabel::where('id',$id)->update([
                            'name'=>$name_temp,
                            'status'=>1,
                            'oid'=>$oid
                        ]);
                    }
                }

                return  $id;
            }
            else
            {
                //查询类别ID
                $movieLabelCategory = MovieLabelCategory::where('name',$category)->firstArray();
                $cid = ($movieLabelCategory['id']??0);
                if($cid <= 0)//如果没有找到类别ID代表无效
                {
                    $this->errorInfo->setCode(500,'无效的类别信息！');
                    return false;
                }

                $movieLabelInfo = MovieLabel::where('name',$name_temp)->where('cid',0)->firstArray(); //必须顶级ID
                $lid = ($movieLabelInfo['id']??0);
                if($lid <= 0)
                {
                    $movieLabelObj = new MovieLabel();
                    $movieLabelObj->setName($name_temp);
                    $movieLabelObj->setStatus(1);
                    $movieLabelObj->setOid($oid);
                    $movieLabelObj->save();
                    $lid = $movieLabelObj->getId();

                    $movieLabelCategoryAssociateobj = new MovieLabelCategoryAssociate();
                    $movieLabelCategoryAssociateobj->setCid($cid);
                    $movieLabelCategoryAssociateobj->setLid($lid);
                    $movieLabelCategoryAssociateobj->setAssociateTime(date('Y-m-d H:i:s'));
                    $movieLabelCategoryAssociateobj->setStatus(1);
                    $movieLabelCategoryAssociateobj->save();
                }
                else
                {
                    if ($status == 4)
                    {
                        MovieLabel::where('id', $lid)->update([
                            'name' => $name_temp,
                            'status' => 1,
                            'oid'=>$oid
                        ]);
                    }

                    $associate = MovieLabelCategoryAssociate::where('lid', $lid)->where('cid', $cid)->firstArray();//查询这些标签关联的类别
                    $aid = ($associate['id'] ?? 0);
                    if ($aid <= 0)
                    {
                        $movieLabelCategoryAssociateobj = new MovieLabelCategoryAssociate();
                        $movieLabelCategoryAssociateobj->setCid($cid);
                        $movieLabelCategoryAssociateobj->setLid($lid);
                        $movieLabelCategoryAssociateobj->setAssociateTime(date('Y-m-d H:i:s'));
                        $movieLabelCategoryAssociateobj->setStatus(1);
                        $movieLabelCategoryAssociateobj->save();
                    }

                }
                return $lid;
            }

        }
        else
        {
            if($name == '')
            {
                $this->errorInfo->setCode(500,'无效的主标签信息！');
                return false;
            }

            $mainLabelId = 0;
            //处理主标签
            if($category == '')
            {
                $movieLabel = MovieLabel::where('name',$name)->firstArray();
                $id = ($movieLabel['id']??0);
                if($id <= 0)
                {
                    $movieLabelObj = new MovieLabel();
                    $movieLabelObj->setName($name);
                    $movieLabelObj->setStatus(1);
                    $movieLabelObj->setOid($oid);
                    $movieLabelObj->save();
                    $id = $movieLabelObj->getId();
                }
                else
                {
                    if($status == 4)
                    {
                        MovieLabel::where('id',$id)->update([
                            'name'=>$name,
                            'status'=>1,
                            'oid'=>$oid
                        ]);
                    }
                }
                $mainLabelId =  $id;
            }
            else
            {
                //查询类别ID
                $movieLabelCategory = MovieLabelCategory::where('name',$category)->firstArray();
                $cid = ($movieLabelCategory['id']??0);
                if($cid <= 0)//如果没有找到类别ID代表无效 - 则按失败处理
                {
                    $this->errorInfo->setCode(500,'无效的类别信息！');
                    return false;
                }

                $movieLabelInfo = MovieLabel::where('name',$name)->where('cid',0)->firstArray(); //必须顶级ID
                $lid = ($movieLabelInfo['id']??0);
                if($lid <= 0)
                {
                    $movieLabelObj = new MovieLabel();
                    $movieLabelObj->setName($name);
                    $movieLabelObj->setStatus(1);
                    $movieLabelObj->setOid($oid);
                    $movieLabelObj->save();
                    $lid = $movieLabelObj->getId();

                    $movieLabelCategoryAssociateobj = new MovieLabelCategoryAssociate();
                    $movieLabelCategoryAssociateobj->setCid($cid);
                    $movieLabelCategoryAssociateobj->setLid($lid);
                    $movieLabelCategoryAssociateobj->setAssociateTime(date('Y-m-d H:i:s'));
                    $movieLabelCategoryAssociateobj->setStatus(1);
                    $movieLabelCategoryAssociateobj->save();
                }
                else
                {
                    if ($status == 4)
                    {
                        MovieLabel::where('id', $lid)->update([
                            'name' => $name,
                            'status' => 1,
                            'oid'=>$oid
                        ]);
                    }

                    $associate = MovieLabelCategoryAssociate::where('lid', $lid)->where('cid', $cid)->firstArray();//查询这些标签关联的类别
                    $aid = ($associate['id'] ?? 0);
                    if ($aid <= 0)
                    {
                        $movieLabelCategoryAssociateobj = new MovieLabelCategoryAssociate();
                        $movieLabelCategoryAssociateobj->setCid($cid);
                        $movieLabelCategoryAssociateobj->setLid($lid);
                        $movieLabelCategoryAssociateobj->setAssociateTime(date('Y-m-d H:i:s'));
                        $movieLabelCategoryAssociateobj->setStatus(1);
                        $movieLabelCategoryAssociateobj->save();
                    }

                }
                $mainLabelId =  $lid;
            }

            if($name_child != '')
            {
                if($category == '')
                {
                    $movieLabel = MovieLabel::where('name',$name_child)->where('cid',$mainLabelId)->firstArray();
                    $id = ($movieLabel['id']??0);
                    if($id <= 0)
                    {
                        $movieLabelObj = new MovieLabel();
                        $movieLabelObj->setName($name_child);
                        $movieLabelObj->setCid($mainLabelId);
                        $movieLabelObj->setStatus(1);
                        $movieLabelObj->save();
                        $id = $movieLabelObj->getId();
                    }
                    else
                    {
                        if($status == 4)
                        {
                            MovieLabel::where('id',$id)->update([
                                'name'=>$name_child,
                                'cid'=>$mainLabelId,
                                'status'=>1,
                                'oid'=>$oid
                            ]);
                        }
                    }
                    return $id;
                }
                else
                {
                    //查询类别ID
                    $movieLabelCategory = MovieLabelCategory::where('name',$category)->firstArray();
                    $cid = ($movieLabelCategory['id']??0);
                    if($cid <= 0)//如果没有找到类别ID代表无效 - 则按失败处理
                    {
                        $this->errorInfo->setCode(500,'无效的类别信息！');
                        return false;
                    }

                    $movieLabelInfo = MovieLabel::where('name',$name_child)->where('cid',$mainLabelId)->firstArray(); //父ID必须一致
                    $lid = ($movieLabelInfo['id']??0);
                    if($lid <= 0)
                    {
                        $movieLabelObj = new MovieLabel();
                        $movieLabelObj->setName($name_child);
                        $movieLabelObj->setCid($mainLabelId);
                        $movieLabelObj->setStatus(1);
                        $movieLabelObj->setOid($oid);
                        $movieLabelObj->save();
                        $lid = $movieLabelObj->getId();

                        $movieLabelCategoryAssociateobj = new MovieLabelCategoryAssociate();
                        $movieLabelCategoryAssociateobj->setCid($cid);
                        $movieLabelCategoryAssociateobj->setLid($lid);
                        $movieLabelCategoryAssociateobj->setAssociateTime(date('Y-m-d H:i:s'));
                        $movieLabelCategoryAssociateobj->setStatus(1);
                        $movieLabelCategoryAssociateobj->save();
                    }
                    else
                    {
                        if ($status == 4)
                        {
                            MovieLabel::where('id', $lid)->update([
                                'name' => $name_child,
                                'cid'=>$mainLabelId,
                                'status' => 1,
                                'oid'=>$oid
                            ]);
                        }

                        $associate = MovieLabelCategoryAssociate::where('lid', $lid)->where('cid', $cid)->firstArray();//查询这些标签关联的类别
                        $aid = ($associate['id'] ?? 0);
                        if ($aid <= 0)
                        {
                            $movieLabelCategoryAssociateobj = new MovieLabelCategoryAssociate();
                            $movieLabelCategoryAssociateobj->setCid($cid);
                            $movieLabelCategoryAssociateobj->setLid($lid);
                            $movieLabelCategoryAssociateobj->setAssociateTime(date('Y-m-d H:i:s'));
                            $movieLabelCategoryAssociateobj->setStatus(1);
                            $movieLabelCategoryAssociateobj->save();
                        }

                    }
                    return  $lid;
                }
            }

            return $mainLabelId;
        }



    }
}