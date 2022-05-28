<?php
/**
 * Created by PhpStorm.
 * User: night
 * Date: 2021/5/19
 * Time: 15:57
 */

namespace App\Model\Logic;


class HandleLogic extends BaseLogic
{

    protected  $namePath = 'App\\Model\\Logic\\';
    protected  $baseClassName = 'BaseLogic';
    //类型执行
    protected  $typeClass = array(

    );

    protected  function getClassName($type)
    {
        return  isset($this->typeClass[$type]) ? $this->namePath.$this->typeClass[$type] : $this->namePath.$this->baseClassName;
    }
}