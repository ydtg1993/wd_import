<?php
/**
 * Created by PhpStorm.
 * User: night
 * Date: 2021/5/26
 * Time: 20:33
 */

namespace App\Model\Logic\MovieData;


use App\Model\Entity\CollectionFilmCompanies;
use App\Model\Entity\MovieFilmCompanies;
use App\Model\Entity\MovieFilmCompaniesCategory;
use App\Model\Entity\MovieFilmCompaniesCategoryAssociate;
use Swoft\Log\Helper\CLog;

class FilmCompaniesLogic extends MovieDataBaseLogic
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

        $count = $categoryData = CollectionFilmCompanies::where('created_at','>=',date('Y-m-d H:i:s',$beginTime))->count();
        $disSum = intval($count);

        $tempIndex = 0;
        $pageIndex = 1;//翻页从第一页开始
        $disCount = 500;

        while ($tempIndex <= $disSum)
        {
            CLog::info('开始处理片商数据 第'.$pageIndex.'页数据！总共'.(ceil($disSum/$disCount)).'页!一次处理数据量：'.$disCount.'条');
            $handleData = CollectionFilmCompanies::where('created_at','>=',date('Y-m-d H:i:s',$beginTime))
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
                    CLog::info('系列数据处理，同步系列数据出错！.错误说明：'.($this->errorInfo->msg??'未知错误'));
                    $status = 5;
                }

                //成功修改状态为已处理
                CollectionFilmCompanies::where('id',$val['id']??0)->update(
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
            $this->errorInfo->setCode(500,'无效的片商数据！');
            return false;
        }

        $oid = ($data['id']??0);
        $name = ($data['name']??'');
        $status = ($data['status']??1);
        $category = ($data['category']??'');

        if($name == '')
        {
            $this->errorInfo->setCode(500,'无效的片商名称！');
            return false;
        }

        if($category == '')
        {
            $movieFilmCompanies = MovieFilmCompanies::where('name',$name)->firstArray();
            $id = ($movieFilmCompanies['id']??0);
            if($id<=0)
            {
                $movieFilmCompaniesObj = new MovieFilmCompanies();
                $movieFilmCompaniesObj->setName($name);
                $movieFilmCompaniesObj->setStatus(1);
                $movieFilmCompaniesObj->setOid($oid);
                $movieFilmCompaniesObj->save();
                $id = $movieFilmCompaniesObj->getId();
            }
            else
            {
                if($status == 4)
                {
                    MovieFilmCompanies::where('id',$id)->update([
                        'name'=>$name,
                        'status'=>1,
                        'oid'=>$oid
                    ]);

                }
            }

            return $id;
        }

        $movieFilmCompaniesCategory = MovieFilmCompaniesCategory::where('name',$category)->firstArray();
        $cid = $movieFilmCompaniesCategory['id']??0;
        if($cid <= 0)
        {
            $this->errorInfo->setCode(500,'无效的片商类别数据！');
            return false;
        }

        $movieFilmCompanies = MovieFilmCompanies::where('name',$name)->firstArray();
        $id = ($movieFilmCompanies['id']??0);
        if($id<=0)
        {
            $movieFilmCompaniesObj = new MovieFilmCompanies();
            $movieFilmCompaniesObj->setName($name);
            $movieFilmCompaniesObj->setStatus(1);
            $movieFilmCompaniesObj->setOid($oid);
            $movieFilmCompaniesObj->save();
            $id = $movieFilmCompaniesObj->getId();

            $associateObj = new MovieFilmCompaniesCategoryAssociate();
            $associateObj->setCid($cid);
            $associateObj->setFilmCompaniesId($id);
            $associateObj->setStatus(1);
            $associateObj->save();

            return $id;
        }

        if($status == 4)
        {
            MovieFilmCompanies::where('id',$id)->update([
                'name'=>$name,
                'status'=>1,
                'oid'=>$oid
            ]);
        }

        //系列类别关联
        $associate = MovieFilmCompaniesCategoryAssociate::where('cid',$cid)->where('film_companies_id',$id)->firstArray();
        if(($associate['id']??0) <= 0)
        {
            $associateObj = new MovieFilmCompaniesCategoryAssociate();
            $associateObj->setCid($cid);
            $associateObj->setFilmCompaniesId($id);
            $associateObj->setStatus(1);
            $associateObj->save();
        }

        return $id;
    }
}