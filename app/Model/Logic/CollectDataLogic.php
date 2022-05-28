<?php
/**
 * Created by PhpStorm.
 * User: night
 * Date: 2021/5/17
 * Time: 15:17
 */

namespace App\Model\Logic;


use App\Http\Common\Common;
use App\Model\Entity\CollectionOriginal;
use App\Model\Logic\DataProcessing\DataProcessingBaseLogic;
use App\Model\Logic\DataProcessing\DataProcessingLogic;
use App\Model\Logic\MovieData\MovieDataLogic;
use Swoft\Log\Helper\CLog;

class CollectDataLogic extends BaseLogic
{
    const DBTABLE_LIST = [
        'javdb',
        'javlibrary',
        //'dmmcojp',
        'fc2',
    ];//视频的

    const ACTOR_LIST = [
        'javdb_actor',
        'javdb_tags',
    ];//其他的

    /**
     * 异步同步数据 - 视频
     * @param bool $isTime
     * @param string $time
     */
    public static function syncMovieData($isTime = true,$time = '')
    {
        $time = ($time == '')?'2021-01-01 00:00:00':$time;//项目是4月份开始的 默认这个世界最好了
        //先获取有多少条数据
        $apiDomain = config('CollectDataApi');
        $apiUrlCount = $apiDomain.'/api/getDataCount';
        $apiUrl = $apiDomain.'/api/getData';
        $disCount = config('DisCountSync');

        $tempDBTabkeList = self::DBTABLE_LIST;
        foreach ($tempDBTabkeList as $dbTableList)
        {
            $time = ($time == '')?'2021-01-01 00:00:00':$time;
            //读取表的时间维度
            if($isTime)
            {
                //自动计算时间
                $timeTemp = CollectionOriginal::where('db_name',$dbTableList)->max('ctime');
                if(!empty($timeTemp))
                {
                    $time = $timeTemp;
                    $time = date('Y-m-d H:i:s',strtotime($time)-(60*60*24*2));//防止同步出错
                }
            }
            
            if($dbTableList == 'javlibrary')
            {
                $time = '2021-01-01 00:00:00';//还原javlibrary 的更新时间临时处理下
            }

            CLog::info('开始处理 '.$dbTableList.' 数据同步！同步起始时间：'.$time);            
            //读取数量
            $countRedata = Common::sendHttpData($apiUrlCount,[
                'tableName'=>$dbTableList,
                'beginTime'=>$time,
            ]);

            if(!$countRedata)
            {
                CLog::info('处理 '.$dbTableList.' 数据同步！同步起始时间：'.$time.' 出现接口错误！');
                continue;
            }
            $countRedata = json_decode($countRedata,true);
            $countRedata = $countRedata['data']??0;
            $tempIndex = 0;
            $pageIndex = 1;//翻页从第一页开始
            $is_run_sync = true;
            //while ($tempIndex <= $countRedata)
            $upTime = strtotime($time);
            $pageIndexCount = 1;//计数器
            while ($is_run_sync)
            {
                CLog::info('处理 '.$dbTableList.' 数据同步！同步起始时间：'.$time.' 当前分页：'.$pageIndex.' 当前计数：'.$pageIndexCount.' 预计计数'.(ceil($countRedata/$disCount)) );
                $reDataTemp = Common::sendHttpData($apiUrl,[
                    'tableName'=>$dbTableList,
                    'beginTime'=>$time,
                    'page'=>$pageIndex,
                    'pageSize'=>$disCount,
                ]);
                $pageIndexCount++;
                if(!$reDataTemp)
                {
                    CLog::info('处理 '.$dbTableList.' 数据同步！同步起始时间：'.$time.' 出现接口错误！'.'分页：'.$pageIndex);
                    continue;
                }
                $reDataTemp = json_decode($reDataTemp,true);
                $reDataTemp = $reDataTemp['data']??[];
                if(count($reDataTemp)<=0)
                {
                    $is_run_sync = false;
                    break;
                }

                foreach ($reDataTemp as $val)
                {
                    $oid = ($val['_id']??[])['$oid']??'';
                    if($oid == '')
                    {
                        continue;
                    }
                    if(($val['uid']??'') == '')
                    {
                        //没有番号的视为无效数据
                        continue;
                    }
                    $dataCollectionOriginal = CollectionOriginal::where('number',$val['uid']??'')->where('oid',$oid)->where('db_name',$dbTableList)->firstArray();
                    if(($dataCollectionOriginal['id']??0)<=0)
                    {
                        $objCollectionOriginal = new  CollectionOriginal();
                        $objCollectionOriginal->setOid($oid);
                        $objCollectionOriginal->setNumber($val['uid']??'');
                        $objCollectionOriginal->setDbName($dbTableList);
                        $objCollectionOriginal->setCtime($val['ctime']??'');
                        $objCollectionOriginal->setData(json_encode($val));
                        $objCollectionOriginal->save();
                    }
                    else
                    {
                        CollectionOriginal::where('id',$dataCollectionOriginal['id']??0)
                            ->update([
                                'db_name'=>$dbTableList,
                                'ctime'=>$val['ctime']??'',
                                'data'=>json_encode($val),
                            ]);
                    }
                }

                $timeTemp = CollectionOriginal::where('db_name',$dbTableList)->max('ctime');
                if(!empty($timeTemp))
                {
                    $time = $timeTemp;//更新下一段的时间
                }
                $pageIndex = (($upTime == strtotime($time))?($pageIndex+1):1);//防止某个时间的数据超过500条
                $upTime = strtotime($time);
                $tempIndex+=$disCount;
            }

        }

    }


    /**
     * 同步属性数据
     */
    public static function syncAttributesData()
    {
        //先获取有多少条数据
        $apiDomain = config('CollectDataApi');
        $apiUrlCount = $apiDomain.'/api/getActorDataCount';
        $apiUrl = $apiDomain.'/api/getActorData';
        $disCount = config('DisCountSync');
        $tempActorList = self::ACTOR_LIST;
        foreach ($tempActorList as $dbTableListActor)
        {
            CLog::info('开始处理 '.$dbTableListActor.' 数据同步-演员数据！');
            //读取数量
            $countRedata = Common::sendHttpData($apiUrlCount,[
                'tableName'=>$dbTableListActor
            ]);

            if(!$countRedata)
            {
                CLog::info('处理 '.$dbTableListActor.' 数据同步！ 出现接口错误！');
                continue;
            }
            $countRedata = json_decode($countRedata,true);
            $countRedata = $countRedata['data']??0;
            $tempIndex = 0;
            $pageIndex = 1;//翻页从第一页开始
            while ($tempIndex <= $countRedata)
            {
                CLog::info('处理 '.$dbTableListActor.' 数据同步！当前分页：'.$pageIndex.' 总分页数量'.(ceil($countRedata/$disCount)) );
                $reDataTemp = Common::sendHttpData($apiUrl,[
                    'tableName'=>$dbTableListActor,
                    'page'=>$pageIndex,
                    'pageSize'=>$disCount,
                ]);
                $pageIndex ++;//增加分页
                if(!$reDataTemp)
                {
                    CLog::info('处理 '.$dbTableListActor.' 数据同步！ 分页：'.$pageIndex);
                    continue;
                }
                $reDataTemp = json_decode($reDataTemp,true);
                $reDataTemp = $reDataTemp['data']??[];

                foreach ($reDataTemp as $val)
                {
                    $oid = ($val['_id']??[])['$oid']??'';
                    if($oid == '')
                    {
                        continue;
                    }
                    $dataCollectionOriginal = CollectionOriginal::where('oid',$oid)->where('db_name',$dbTableListActor)->firstArray();
                    if(($dataCollectionOriginal['id']??0)<=0)
                    {
                        $objCollectionOriginal = new  CollectionOriginal();
                        $objCollectionOriginal->setOid($oid);
                        $objCollectionOriginal->setNumber($val['uid']??'');
                        $objCollectionOriginal->setDbName($dbTableListActor);
                        $objCollectionOriginal->setData(json_encode($val));
                        $objCollectionOriginal->save();
                    }
                    else
                    {
                        CollectionOriginal::where('id',$dataCollectionOriginal['id']??0)
                            ->update([
                                'db_name'=>$dbTableListActor,
                                'data'=>json_encode($val),
                            ]);
                    }
                }
                $tempIndex+=$disCount;
            }

        }
    }

    /**
     * 处理所有未处理过的数据
     */
    public static function handleDataAll()
    {
        //读取未处的数据量
        CLog::info('开始处理数据！');
        $disSum = CollectionOriginal::where('status',1)->count();
        CLog::info('需要处理数据总数：'.$disSum.'！');
        $tempIndex = 0;
        $pageIndex = 1;//翻页从第一页开始
        $disCount = 500;
        while ($tempIndex <= $disSum)
        {
            CLog::info('开始处理第'.$pageIndex.'页数据！总共'.(ceil($disSum/$disCount)).'页!一次处理数据量：'.$disCount.'条');
            $handleData = CollectionOriginal::where('status',1)->offset((($pageIndex - 1 ) * $disCount)<= 0 ? 0:(($pageIndex - 1 ) * $disCount))
                ->limit($disCount)->get();
            $tempIndex += $disCount;
            $pageIndex++;
            foreach ($handleData as $val)
            {
                $dataProcess = new DataProcessingLogic();
                $reData = $dataProcess->resolveHandle($val['data']??'',$val['id']??0,$val['db_name']??'');
                $handleError = $dataProcess->getError();
                if(($handleError->code??500) != 200)
                {
                    CLog::info('处理数据番号：'.($val['number']??'') .'出错！出错说明：'.($handleError->msg??'未知错误'));
                }
            }

        }

    }


    /**
     * 同步资源数据
     */
    public static function syncResourcesData()
    {
        $index = 0;
        CLog::info('资源同步处理开始！');
        while (true)
        {

            if(($index % 1000) == 0)
            {
                CLog::info('资源同步处理！已经同步处理量：'.$index);
            }

            $data = DataProcessingBaseLogic::getResourcesHandleQueue();
            if($data == null)
            {
                return true;
            }

            if(($data['type']??'') != '')
            {
                $obj = new DataProcessingLogic();
                $obj->downResources($data);
                $index++;
            }
        }
    }

    public static function movieDataDis()
    {
        CLog::info('影片数据自动处理开始！');
        $obj = new MovieDataLogic();
        $obj->MovieDataDis();
        CLog::info('影片数据自动处理完成！');
    }


    /**
     * 磁链数据同步
     */
    public static function syncMovieDataFluxLinkage()
    {
        $time = '2021-01-01 00:00:00';//项目是4月份开始的 默认这个世界最好了
        //先获取有多少条数据
        $apiDomain = config('CollectDataApi');
        $apiUrlCount = $apiDomain.'/api/getDataFluxLinkageCount';
        $apiUrl = $apiDomain.'/api/getDataFluxLinkage';
        $disCount = config('DisCountSync');

        $tempDBTabkeListFL = self::DBTABLE_LIST;
        foreach ($tempDBTabkeListFL as $dbTableListFL)
        {
            //读取表的时间维度
            //自动计算时间
            $time = date('Y-m-d H:i:s',time() - (60*60*24*2));//读取最近两天的磁链更新时间

            CLog::info('开始处理 '.$dbTableListFL.' 数据同步！同步起始时间：'.$time);
            //读取数量
            $countRedata = Common::sendHttpData($apiUrlCount,[
                'tableName'=>$dbTableListFL,
                'beginTime'=>$time,
            ]);

            if(!$countRedata)
            {
                CLog::info('处理 '.$dbTableListFL.' 数据同步！同步起始时间：'.$time.' 出现接口错误！');
                continue;
            }
            $countRedata = json_decode($countRedata,true);
            $countRedata = $countRedata['data']??0;
            $tempIndex = 0;
            $pageIndex = 1;//翻页从第一页开始
            while ($tempIndex <= $countRedata)
            {
                CLog::info('处理 '.$dbTableListFL.' 数据同步！同步起始时间：'.$time.' 当前分页：'.$pageIndex.' 总分页数量'.(ceil($countRedata/$disCount)) );
                $reDataTemp = Common::sendHttpData($apiUrl,[
                    'tableName'=>$dbTableListFL,
                    'beginTime'=>$time,
                    'page'=>$pageIndex,
                    'pageSize'=>$disCount,
                ]);

                $pageIndex ++;//增加分页
                if(!$reDataTemp)
                {
                    CLog::info('处理 '.$dbTableListFL.' 数据同步！同步起始时间：'.$time.' 出现接口错误！'.'分页：'.$pageIndex);
                    continue;
                }
                $reDataTemp = json_decode($reDataTemp,true);
                $reDataTemp = $reDataTemp['data']??[];

                foreach ($reDataTemp as $val)
                {
                    $oid = ($val['_id']??[])['$oid']??'';
                    if($oid == '')
                    {
                        continue;
                    }
                    if(($val['uid']??'') == '')
                    {
                        //没有番号的视为无效数据
                        continue;
                    }
                    //磁链数据处理
                    $val['type'] = $dbTableListFL;
                    $obj = new DataProcessingLogic();
                    $obj->fluxLinkage($val);
                }

                $tempIndex+=$disCount;
            }

        }
    }
}