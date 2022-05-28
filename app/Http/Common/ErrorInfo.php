<?php
/**
 * Created by PhpStorm.
 * User: night
 * Date: 2021/5/14
 * Time: 9:45
 */

namespace App\Http\Common;


class ErrorInfo
{
    public $data = array();
    public $msg = '';
    public $code = 200;


    protected $msgCode = array(
        200 => '成功！',
    );

    public function getMsg($code)
    {
        return self::$msgCode[$code]??'未知错误';
    }

    public function setCode($code,$msg = '')
    {
        $this->code = $code;
        $this->msg = ($msg == '') ? ($this->msgCode[$code]??'未知错误'):$msg;
        return $this;
    }

    public function setData($data = array(),$code = 200,$msg = '')
    {
        $this->data = $data;
        return $this->setCode($code,$msg);
    }

    public function  reset()
    {
        $this->data = [];
        $this->code = 200;
        $this->msg = self::$msgCode[$this->code]??'未知错误';
    }
}