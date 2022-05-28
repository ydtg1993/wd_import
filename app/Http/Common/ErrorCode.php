<?php
/**
 * Created by PhpStorm.
 * User: night
 * Date: 2021/5/14
 * Time: 9:45
 */

namespace App\Http\Common;


class ErrorCode
{

    const SUCCESS = 200 ; //成功

    //参数错误 parameter
    const ERROR_PARAMETER_MISS = 201;//缺少必填参数
    const ERROR_PARAMETER_TYPE = 202;//参数类型错误

    const ERROR_UNKNOWN = 500 ; //未知错误 常规错误


    //TOKEN错误编码Failure
    const ERROR_TOKEN_VERIFICATION = 400;//签名错误检测失败
    const ERROR_TOKEN_RESET = 300;//签名需要刷新后才能使用
    const ERROR_TOKEN_SIGNA = 301;//签名错误
    const ERROR_TOKEN_FAILURE = 302;//token失效



    //用户错误编码 400
    const ERROR_USER_NULL = 400;//无效用户
    const ERROR_USER_EMAIL = 420;//不是合法邮箱

    const ERROR_CODE = [
        self::SUCCESS=>'成功！',
        self::ERROR_UNKNOWN=>'未知错误！',
        self::ERROR_PARAMETER_MISS=>'缺少必填参数！',
        self::ERROR_PARAMETER_TYPE=>'参数类型错误！',

        //签名说明
        self::ERROR_TOKEN_SIGNA=>'签名错误！',
        self::ERROR_TOKEN_FAILURE=>'token失效！',
        self::ERROR_TOKEN_VERIFICATION=>'签名错误检测失败！',
        self::ERROR_TOKEN_RESET=>'签名过期需要刷新！',

        //用户错误编码
        self::ERROR_USER_NULL=>'无效用户！',
        self::ERROR_USER_EMAIL=>'不是合法邮箱！',

    ];
}