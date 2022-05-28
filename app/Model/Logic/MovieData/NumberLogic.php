<?php
/**
 * Created by PhpStorm.
 * User: night
 * Date: 2021/5/27
 * Time: 17:25
 */

namespace App\Model\Logic\MovieData;


use App\Model\Entity\CollectionNumber;
use App\Model\Entity\MovieNumber;
use Swoft\Log\Helper\CLog;

class NumberLogic extends MovieDataBaseLogic
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

        $count = CollectionNumber::where('created_at','>=',date('Y-m-d H:i:s',$beginTime))
            ->count();
        $disSum = intval($count);

        $tempIndex = 0;
        $pageIndex = 1;//翻页从第一页开始
        $disCount = 500;

        while ($tempIndex <= $disSum)
        {
            CLog::info('开始处理番号数据 第'.$pageIndex.'页数据！总共'.(ceil($disSum/$disCount)).'页!一次处理数据量：'.$disCount.'条');
            $handleData = CollectionNumber::where('created_at','>=',date('Y-m-d H:i:s',$beginTime))
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
                $status= 3;
                if(($this->errorInfo->code??500)!=200)
                {
                    CLog::info('番号数据处理，同步番号数据出错！.错误说明：'.($this->errorInfo->msg??'未知错误'));
                    $status = 5;
                }

                //成功修改状态为已处理
                CollectionNumber::where('id',$val['id']??0)->update(
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
            $this->errorInfo->setCode(500,'无效的导演数据！');
            return false;
        }

        $oid = ($data['id']??0);
        $name = ($data['name']??'');
        $status = ($data['status']??1);

        if($name == '')
        {
            $this->errorInfo->setCode(500,'无效的导演名称！');
            return false;
        }

        $movieNumber = MovieNumber::where('name',$name)->firstArray();
        $id = ($movieNumber['id']??0);
        if($id<=0)
        {
            $movieNumberObj = new MovieNumber();
            $movieNumberObj->setName($name);
            $movieNumberObj->setStatus(1);
            $movieNumberObj->setOid($oid);
            $movieNumberObj->save();
            $id = $movieNumberObj->getId();
        }
        else
        {
            if($status == 4)
            {
                MovieNumber::where('id',$id)->update([
                    'name'=>$name,
                    'status'=>1,
                    'oid'=>$oid
                ]);
            }
        }

        return $id;
    }
}