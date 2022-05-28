<?php
/**
 * Created by PhpStorm.
 * User: night
 * Date: 2021/5/26
 * Time: 17:02
 */

namespace App\Model\Logic\MovieData;


use App\Model\Entity\CollectionDirector;
use App\Model\Entity\MovieDirector;
use Swoft\Log\Helper\CLog;

class DirectorLogic extends MovieDataBaseLogic
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

        $count = $categoryData = CollectionDirector::where('created_at','>=',date('Y-m-d H:i:s',$beginTime))->count();
        $disSum = intval($count);

        $tempIndex = 0;
        $pageIndex = 1;//翻页从第一页开始
        $disCount = 500;

        while ($tempIndex <= $disSum)
        {
            CLog::info('开始处理导演数据 第'.$pageIndex.'页数据！总共'.(ceil($disSum/$disCount)).'页!一次处理数据量：'.$disCount.'条');
            $handleData = CollectionDirector::where('created_at','>=',date('Y-m-d H:i:s',$beginTime))
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
                    CLog::info('导演数据处理，同步导演数据出错！.错误说明：'.($this->errorInfo->msg??'未知错误'));
                    $status = 5;
                }

                //成功修改状态为已处理
                CollectionDirector::where('id',$val['id']??0)->update(
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

        $movieDirector = MovieDirector::where('name',$name)->firstArray();
        $id = ($movieDirector['id']??0);
        if($id<=0)
        {
            $movieDirectorObj = new MovieDirector();
            $movieDirectorObj->setName($name);
            $movieDirectorObj->setStatus(1);
            $movieDirectorObj->setOid($oid);
            $movieDirectorObj->save();
            $id = $movieDirectorObj->getId();
        }
        else
        {
            if($status == 4)
            {
                MovieDirector::where('id',$id)->update([
                    'name'=>$name,
                    'status'=>1,
                    'oid'=>$oid,
                ]);
            }
        }

        return $id;
    }
}