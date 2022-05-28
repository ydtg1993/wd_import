<?php
/**
 * Created by PhpStorm.
 * User: night
 * Date: 2021/5/17
 * Time: 16:42
 */

namespace App\Model\Logic;


use App\Http\Common\ErrorInfo;

class BaseLogic
{
    protected $errorInfo = null;

    function __construct()
    {
        $this->errorInfo = new ErrorInfo();
    }

    public function getError()
    {
        return $this->errorInfo == null ? (new  ErrorInfo()):$this->errorInfo;
    }
}