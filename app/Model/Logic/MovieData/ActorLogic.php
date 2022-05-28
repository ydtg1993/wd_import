<?php
/**
 * Created by PhpStorm.
 * User: night
 * Date: 2021/5/27
 * Time: 10:53
 */

namespace App\Model\Logic\MovieData;


use App\Model\Entity\ActorPopularityChart;
use App\Model\Entity\CollectionActor;
use App\Model\Entity\CollectionActorName;
use App\Model\Entity\Movie;
use App\Model\Entity\MovieActor;
use App\Model\Entity\MovieActorAssociate;
use App\Model\Entity\MovieActorCategory;
use App\Model\Entity\MovieActorCategoryAssociate;
use App\Model\Entity\MovieActorName;
use App\Model\Entity\MovieCategoryAssociate;
use Swoft\Db\DB;
use Swoft\Log\Helper\CLog;

class ActorLogic extends MovieDataBaseLogic
{
    /**
     * 数据处理
     */
    public function dataRun()
    {
        $time = intval(config('WaitingDataTime',48));
        //$time = ($time<2||($time>(24*15)))?48:$time;
        $time = $time*60*60;
        //$time=0;
        $beginTime = time() - $time;

        $count = CollectionActor::where('created_at','>=',date('Y-m-d H:i:s',$beginTime))->count();
        $disSum = intval($count);

        $tempIndex = 0;
        $pageIndex = 1;//翻页从第一页开始
        $disCount = 500;

        while ($tempIndex <= $disSum)
        {
            CLog::info('开始处理演员数据 第'.$pageIndex.'页数据！总共'.(ceil($disSum/$disCount)).'页!一次处理数据量：'.$disCount.'条');
            $handleData = CollectionActor::where('created_at','>=',date('Y-m-d H:i:s',$beginTime))
                ->offset((($pageIndex - 1 ) * $disCount)<= 0 ? 0:(($pageIndex - 1 ) * $disCount))
                ->limit($disCount)->get();

            foreach ($handleData as $val)
            {
                $statusTemp = $val['status']??0;
                $resources_status = $val['resources_status']??0;

                if($resources_status != 2)
                {
                    continue;
                }
                if(!($statusTemp == 1 || $statusTemp == 4 ))
                {
                    continue;
                }
                $this->errorInfo->reset();//重置错误信息
                $this->dataDis($val);
                $status= 3;
                if(($this->errorInfo->code??500)!=200)
                {
                    CLog::info('演员数据处理，同步演员数据出错！.错误说明：'.($this->errorInfo->msg??'未知错误'));
                    $status = 5;
                }

                //成功修改状态为已处理
                CollectionActor::where('id',$val['id']??0)->update(
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
            $this->errorInfo->setCode(500,'无效的演员数据！');
            return false;
        }

        $oid = ($data['id']??0);
        $name = ($data['name']??'');
        $sex = ($data['sex']??'');
        $category = ($data['category']??'');
        $status = ($data['status']??1);
        $interflow = json_decode($data['interflow']??'',true);
        $social_accounts = json_decode($data['social_accounts']??'',true);
        $social_accounts = array_merge($interflow??[],$social_accounts??[]);

        $resources_info = json_decode($data['resources_info']??'',true);
        $avatar = $resources_info['avatar']??'';

        if($name == '')
        {
            $this->errorInfo->setCode(500,'无效的演员名称！');
            return false;
        }

        //无码和有码，强制中文
        if(($category =='无码' || $category =='有码') && self::chkAllChinese($name)==false ){
            $this->errorInfo->setCode(500,'无码和有码时，演员名称必须中文');
            return false;
        }

        //查询演员类别
        $cid = 0;
        if($category != '')
        {
            $actorCategory = MovieActorCategory::where('name',$category)->firstArray();
            $cid = $actorCategory['id']??0;
        }

        $actorNameInfo  = MovieActorName::where('name',$name)->firstArray();
        $actorId = $actorNameInfo['aid']??0;
        if($actorId <= 0)//还没有该演员 - 只是主名称没有
        {
            //读取该演员的所有名称
            $names = CollectionActorName::where('aid',$oid)->get()->pluck('name')->toArray();
            if(is_array($names) && count($names) > 0)
            {
                $actorIdTemp = 0;
                foreach ($names as $val)
                {
                    $actorTempNameInfo  = MovieActorName::where('name',$val)->firstArray();
                    $actorIdTemp = $actorTempNameInfo['aid']??0;
                    if($actorIdTemp > 0)
                    {
                        break;
                    }
                }

                if($actorIdTemp > 0)//有演员
                {
                    foreach ($names as $val)
                    {
                        $actorTempNameInfoAdd  = MovieActorName::where('name',$val)->firstArray();
                        $actorIdTempAdd = $actorTempNameInfoAdd['aid']??0;
                        if($actorIdTempAdd <= 0)
                        {
                            $MovieActor = new MovieActorName();
                            $MovieActor->setName($val);
                            $MovieActor->setAid($actorIdTemp);
                            $MovieActor->save();
                        }
                    }

                    //检查类别关联
                    if($cid > 0)
                    {
                        $actorCategoryAssociate = MovieActorCategoryAssociate::where('cid',$cid)->where('aid',$actorIdTemp)->firstArray();
                        if(($actorCategoryAssociate['id']??0) <= 0)
                        {
                            $actorCategoryAssociateObj = new MovieActorCategoryAssociate();
                            $actorCategoryAssociateObj->setAid($actorIdTemp);
                            $actorCategoryAssociateObj->setCid($cid);
                            $actorCategoryAssociateObj->setStatus(1);
                            $actorCategoryAssociateObj->save();
                        }
                    }
                    return $actorIdTemp;
                }

            }

            $actorObj = new MovieActor();
            $actorObj->setStatus(1);
            $actorObj->setName($name);
            $actorObj->setSex($sex);
            $actorObj->setPhoto($avatar);
            $actorObj->setOid($oid);
            $actorObj->setSocialAccounts(json_encode($social_accounts));
            $actorObj->save();

            $actorId = $actorObj->getId();

            //先将当前名称插入 名称分表
            $MovieActor = new MovieActorName();
            $MovieActor->setName($name);
            $MovieActor->setAid($actorId);
            $MovieActor->save();

            if($cid > 0)
            {
                $actorCategoryAssociate = MovieActorCategoryAssociate::where('cid',$cid)->where('aid',$actorId)->firstArray();
                if(($actorCategoryAssociate['id']??0) <= 0)
                {
                    $actorCategoryAssociateObj = new MovieActorCategoryAssociate();
                    $actorCategoryAssociateObj->setAid($actorId);
                    $actorCategoryAssociateObj->setCid($cid);
                    $actorCategoryAssociateObj->setStatus(1);
                    $actorCategoryAssociateObj->save();
                }
            }

            if(is_array($names) && count($names) > 0)
            {
                foreach ($names as $val)
                {
                    $actorTempNameInfoAdd  = MovieActorName::where('name',$val)->firstArray();
                    $actorIdTempAdd = $actorTempNameInfoAdd['aid']??0;
                    if($actorIdTempAdd <= 0)
                    {
                        $MovieActor = new MovieActorName();
                        $MovieActor->setName($val);
                        $MovieActor->setAid($actorId);
                        $MovieActor->save();
                    }
                }
            }

            return $actorId;
        }
        else
        {
            //存在该演员
            if($status == 4)
            {
                MovieActor::where('id',$actorId)->update(
                    [
                        'name'=>$name,
                        'photo'=>$avatar,
                        'sex'=>$sex,
                        'oid'=>$oid,
                        'social_accounts'=>json_encode($social_accounts),
                        'status'=>1,
                    ]
                );
            }

            if($cid > 0)
            {
                $actorCategoryAssociate = MovieActorCategoryAssociate::where('cid',$cid)->where('aid',$actorId)->firstArray();
                if(($actorCategoryAssociate['id']??0) <= 0)
                {
                    $actorCategoryAssociateObj = new MovieActorCategoryAssociate();
                    $actorCategoryAssociateObj->setAid($actorId);
                    $actorCategoryAssociateObj->setCid($cid);
                    $actorCategoryAssociateObj->setStatus(1);
                    $actorCategoryAssociateObj->save();
                }
            }

            $names = CollectionActorName::where('aid',$oid)->get()->pluck('name')->toArray();
            if(is_array($names) && count($names) > 0)
            {
                foreach ($names as $val)
                {
                    $actorTempNameInfoAdd  = MovieActorName::where('name',$val)->firstArray();
                    $actorIdTempAdd = $actorTempNameInfoAdd['aid']??0;
                    if($actorIdTempAdd <= 0)
                    {
                        $MovieActor = new MovieActorName();
                        $MovieActor->setName($val);
                        $MovieActor->setAid($actorId);
                        $MovieActor->save();
                    }
                }
            }
        }

        return $actorId;
    }


    /**
     * 演员热度计算
     */
    public static function hotDis()
    {
        $type = MovieActorCategory::pluck('id')->all();
        $time = date('Y-m-01 00:00:00',time());//本月时间
        foreach ($type as $val)
        {
            $page = 0;
            $pageSize = 500;//一次处理500条
            $isDis = true;
            while ($isDis)
            {
                $page++;
                $aids = MovieActorCategoryAssociate::where('cid',$val)->where('status',1)->offset((($page - 1 ) * $pageSize)<= 0 ? 0:(($page - 1 ) * $pageSize))
                    ->limit($pageSize)->get()->pluck('aid')->toArray();

                if(count($aids)<=0)
                {
                    $isDis = false;
                }
                foreach ($aids as $aidVal)
                {
                    //读取演员影片ID
                    $objMidInfos = MovieActorAssociate::where('movie_actor_associate.aid',$aidVal)->where('movie_actor_associate.status',1)->get()->pluck('mid')->toArray();
                    if(count($objMidInfos) <= 0)
                    {
                        continue;
                    }

                    //超过1000处理
                    if(count($objMidInfos) >= 1000)
                    {
                        $newMidCount = 0;
                        $newMids = [];
                        $newMidCountPv = 0;
                        $wan_see_num = 0;
                        $seenNum = 0;
                        $comment_numNum = 0;

                        $newobjMidInfos = array_chunk($objMidInfos,500);
                        foreach ($newobjMidInfos as $newobjMidInfosVal)
                        {
                            $objMidsTemp = Movie::whereIn('id',$newobjMidInfosVal) ->where('movie.release_time','>=',$time);
                            $newMidsTemp = $objMidsTemp->get()->pluck('id')->toArray();

                            $newMidCount += $objMidsTemp->count();
                            $wan_see_num += $objMidsTemp->sum('movie.wan_see');//想看数量
                            $seenNum += $objMidsTemp->sum('movie.seen');//看过数量
                            $comment_numNum += $objMidsTemp->sum('movie.comment_num');//评论数量

                            foreach ($newMidsTemp as $newMidsValue)
                            {
                                $newMids[] = $newMidsValue;
                            }
                        }

                        if($newMidCount > 500)
                        {
                            $newMidsTemp = array_chunk($newMids,500);
                            foreach ($newMidsTemp as $midVals)
                            {
                                if(count($midVals) >0)
                                {
                                    $newMidCountPv = $newMidCountPv + DB::table('movie_log')->where('created_at', '>=', $time)->whereIn('mid', $midVals)->count();
                                }
                            }
                        }
                        else
                        {
                            if(count($newMids) >0)
                            {
                                $newMidCountPv = DB::table('movie_log')->where('created_at','>=',$time)->whereIn('mid',$newMids)->count();
                            }
                        }

                        $newMidCountPv = $newMidCountPv / 500;//浏览数量

                        $wan_see_num = $newMidCount>0?($wan_see_num / $newMidCount):0;
                        $seenNum = $newMidCount>0?($seenNum / $newMidCount):0;
                        $comment_numNum = $newMidCount>0?($comment_numNum / $newMidCount):0;

                        $hotVal = $comment_numNum+$seenNum+$wan_see_num+$newMidCount+$newMidCountPv;

                        $actorPopularityChartInfo = ActorPopularityChart::where('aid' ,$aidVal)->where('cid',$val)->where('mtime',$time)->firstArray();

                        if(($actorPopularityChartInfo['id']??0)<=0)
                        {
                            $timeUp = date('Y-m-01 00:00:00',strtotime($time)-(60*60*24));
                            $actorPopularityChartUpInfo = ActorPopularityChart::where('aid' ,$aidVal)->where('cid',$val)->where('mtime',$timeUp)->firstArray();
                            $hotValUp = $actorPopularityChartUpInfo['hot_val']??0;

                            $actorPopularityChartObj = new ActorPopularityChart();
                            $actorPopularityChartObj->setCid($val);
                            $actorPopularityChartObj->setAid($aidVal);
                            $actorPopularityChartObj->setMtime($time);
                            $actorPopularityChartObj->setHotVal($hotVal);
                            $actorPopularityChartObj->setUpMhot($hotValUp);
                            $actorPopularityChartObj->save();
                        }
                        else
                        {
                            ActorPopularityChart::where('aid' ,$aidVal)->where('cid',$val)->where('mtime',$time)->update([
                                'hot_val'=>$hotVal
                            ]);
                        }

                        continue;
                    }

                    //没超过 1000处理

                    $objMids = Movie::whereIn('id',$objMidInfos) ->where('movie.release_time','>=',$time);
                    /*$objMids = MovieActorAssociate::where('movie_actor_associate.aid',$aidVal)->where('movie_actor_associate.status',1)
                        ->leftJoin('movie','movie.id','=','movie_actor_associate.mid')
                        ->where('movie.release_time','>=',$time);*/
                    $newMidCount  = $objMids->count();//新增影片数量
                    $newMidCountPv = 0;
                    $newMids = $objMids->get()->pluck('id')->toArray();
                    if($newMidCount > 500)
                    {
                        $newMidsTemp = array_chunk($newMids,500);
                        foreach ($newMidsTemp as $midVals)
                        {
                            if(count($midVals) >0)
                            {
                                $newMidCountPv = $newMidCountPv + DB::table('movie_log')->where('created_at', '>=', $time)->whereIn('mid', $midVals)->count();
                            }
                        }
                    }
                    else
                    {
                        if(count($newMids) >0)
                        {
                            $newMidCountPv = DB::table('movie_log')->where('created_at','>=',$time)->whereIn('mid',$newMids)->count();
                        }

                    }

                    $newMidCountPv = $newMidCountPv / 500;//浏览数量

                    $wan_see_num = $objMids->sum('movie.wan_see');//想看数量
                    $seenNum = $objMids->sum('movie.seen');//看过数量
                    $comment_numNum = $objMids->sum('movie.comment_num');//评论数量

                    $wan_see_num = $newMidCount>0?($wan_see_num / $newMidCount):0;
                    $seenNum = $newMidCount>0?($seenNum / $newMidCount):0;
                    $comment_numNum = $newMidCount>0?($comment_numNum / $newMidCount):0;

                    $hotVal = $comment_numNum+$seenNum+$wan_see_num+$newMidCount+$newMidCountPv;

                    $actorPopularityChartInfo = ActorPopularityChart::where('aid' ,$aidVal)->where('cid',$val)->where('mtime',$time)->firstArray();

                    if(($actorPopularityChartInfo['id']??0)<=0)
                    {
                        $timeUp = date('Y-m-01 00:00:00',strtotime($time)-(60*60*24));
                        $actorPopularityChartUpInfo = ActorPopularityChart::where('aid' ,$aidVal)->where('cid',$val)->where('mtime',$timeUp)->firstArray();
                        $hotValUp = $actorPopularityChartUpInfo['hot_val']??0;

                        $actorPopularityChartObj = new ActorPopularityChart();
                        $actorPopularityChartObj->setCid($val);
                        $actorPopularityChartObj->setAid($aidVal);
                        $actorPopularityChartObj->setMtime($time);
                        $actorPopularityChartObj->setHotVal($hotVal);
                        $actorPopularityChartObj->setUpMhot($hotValUp);
                        $actorPopularityChartObj->save();
                    }
                    else
                    {
                        ActorPopularityChart::where('aid' ,$aidVal)->where('cid',$val)->where('mtime',$time)->update([
                            'hot_val'=>$hotVal
                        ]);
                    }
                }
            }


        }

    }

    /**
     * 判断是否全文是中文
     * */
    public static function chkAllChinese($str)
    {
        if(preg_match('/^[\x{4e00}-\x{9fa5}]+$/u',$str)>0){
            return true;
        }
        return false;
    }
}
