<?php
/**
 * Created by PhpStorm.
 * User: night
 * Date: 2021/5/19
 * Time: 15:52
 */

namespace App\Model\Logic\DataProcessing;


use App\Model\Logic\HandleLogic;

class DataProcessingLogic extends HandleLogic
{
    protected  $namePath = 'App\\Model\\Logic\\DataProcessing\\';
    protected  $baseClassName = 'DataProcessingBaseLogic';

    //类型执行
    protected  $typeClass = array(
        'base'=>'DataProcessingBaseLogic',
        'javdb_actor'=>'ActorLogic',
        'javdb_tags'=>'TagLogic',
        'javdb'=>'JavdbLogic',
        'javlibrary'=>'JavlibraryLogic',
        'fc2'=>'Fc2Logic',
        //'dmmcojp'=>'DmmcojpLogic',
    );

    /**
     * 解析数据来源的数据 将解析的数据进行格式化处理并保存到数据中
     * @param string $data
     * @param int $id
     * @param string $type
     * @return array
     */
    public  function resolveHandle($data = '',$id = 0,$type='base')
    {
        $data = json_decode($data,true);
        $data['db_name'] = $type;
        $runClassName =  $this->getClassName($type);
        if(!class_exists($runClassName))
        {
            $this->errorInfo->setCode('500','无效的处理对象！');
            return [];
        }
        $obj = new  $runClassName;
        $redata = $obj->resolveHandle($data,$id);
        $this->errorInfo = $obj->getError();
        if(($this->errorInfo->code??500) == 200)
        {
            $obj->updateOriginalData($id);
        }
        return $redata;
    }

    /**
     * 下载资源处理
     * @param array $data
     * @return array
     */
    public function downResources($data = [])
    {
        $runClassName =  $this->getClassName($data['type']??'base');
        if(!class_exists($runClassName))
        {
            $this->errorInfo->setCode('500','无效的处理对象！');
            return [];
        }
        $obj = new  $runClassName;
        $redata = $obj->downResources($data);
        $this->errorInfo = $obj->getError();
        return $redata;
    }

    /**
     * 磁链更新处理
     * @param array $data
     * @return array
     */
    public function fluxLinkage($data = [])
    {
        $runClassName =  $this->getClassName($data['type']??'base');
        if(!class_exists($runClassName))
        {
            $this->errorInfo->setCode('500','无效的处理对象！');
            return [];
        }
        $obj = new  $runClassName;
        $redata = $obj->fluxLinkage($data);
        $this->errorInfo = $obj->getError();
        return $redata;
    }


}