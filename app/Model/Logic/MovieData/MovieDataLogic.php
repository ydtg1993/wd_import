<?php
/**
 * Created by PhpStorm.
 * User: night
 * Date: 2021/5/26
 * Time: 15:46
 */

namespace App\Model\Logic\MovieData;


use App\Model\Logic\HandleLogic;
use Swoft\Log\Helper\CLog;

class MovieDataLogic extends HandleLogic
{
    protected  $namePath = 'App\\Model\\Logic\\MovieData\\';
    protected  $baseClassName = 'MovieDataBaseLogic';

    //类型执行
    protected  $typeClass = array(
        'base'=>'MovieDataBaseLogic',//默认
        'category'=>'CategoryLogic',//类别
        'director'=>'DirectorLogic',//导演
        'Number'=>'NumberLogic',//番号
        'filmCompanies'=>'FilmCompaniesLogic',//片商
        'label'=>'LabelLogic',//标签
        'series'=>'SeriesLogic',//系列
        'actor'=>'ActorLogic',//演员
        'movie'=>'MovieLogic',//影片
    );

    public function MovieDataDis()
    {
        foreach ($this->typeClass as $k=>$v)
        {
            $runClassName = $this->getClassName($k);
            if(!class_exists($runClassName))
            {
                continue;
            }
            else
            {
                $dbObj = new  $runClassName;
                $dbObj->dataRun();
            }
        }
    }

    /**
     * 制定数据处理
     * @param $k
     * @return array
     */
    public function dataRun($k)
    {
        $runClassName =  $this->getClassName($k);
        if(!class_exists($runClassName))
        {
            $this->errorInfo->setCode('500','无效的处理对象！');
            return [];
        }
        $obj = new  $runClassName;
        $redata = $obj->dataRun();
        $this->errorInfo = $obj->getError();
        return $redata;
    }


}
