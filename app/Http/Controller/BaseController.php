<?php
/**
 * Created by PhpStorm.
 * User: night
 * Date: 2021/5/14
 * Time: 14:30
 */

namespace App\Http\Controller;
use App\Http\Common\ErrorCode;
use App\Http\Common\ErrorInfo;
use Swoft\Http\Server\Annotation\Mapping\Controller;
use Swoft\Http\Server\Annotation\Mapping\RequestMapping;
use Swoft\Bean\Annotation\Inject;

use function context;


class BaseController
{
    /**
     * 参数过滤
     * @param array $temps 模板数据
     * @param array $param 过滤的参数
     * @return array|bool
     */
    public function paramFilter(array $temps,array $param)
    {
        //类型检查
        $data = [];
        foreach ($temps as $k=>$v)
        {
            if(isset($param[$k]))
            {
                if(settype($param[$k],gettype($v)))
                {
                    $data[$k] = $param[$k];
                }
                else
                {
                    return false;
                }
            }
            else
            {
                $data[$k] = $v;
            }
        }
        return $data;
    }

    /**
     * 必填参数检测
     * @param array $temps
     * @param array $param
     * @return bool
     */
    public function haveToParam(array $temps,array $param)
    {
        foreach ($temps as $k=>$v)
        {
            if(!isset($param[$k]))
            {
                return false;
            }
        }
        return true;
    }

    /**
     * 自定义JSON返回
     * @param $data
     * @param int $code
     * @param string $msg
     * @return \Swoft\Http\Message\Response|\Swoft\Rpc\Server\Response
     * @throws \Swoft\Exception\SwoftException
     */
    protected function sendJson($data = null,$code = 200,$msg = '')
    {
        return context()->getResponse()
            ->withHeader("Access-Control-Allow-Origin", "*")
            ->withHeader("Access-Control-Allow-Header", "X-Request-With, Content-Type,token, Accept, Origin, Authorization")
            ->withHeader("Access-Control-Allow-Methods", "GET, POST, PUT, DELETE, PATCH, OPTIONS")
            ->withData(
                array(
                    'data'=>$data,
                    'msg'=>$msg == ''?(isset(ErrorCode::ERROR_CODE[$code])?ErrorCode::ERROR_CODE[$code]:'未知错误'):$msg ,
                    'code'=>$code
                ));
    }

    /**
     * @param $data
     * @return \Swoft\Http\Message\Response|\Swoft\Rpc\Server\Response
     * @throws \Swoft\Exception\SwoftException
     */
    protected function sendInfo($data)
    {
        $data = $data == null?(new ErrorInfo()):$data;
        return $this->sendJson($data->data??array(),$data->code??200,$data->msg??'成功！');
    }


    /**
     * 错误返回
     * @param string $msg
     * @param int $code
     * @param string $data
     * @return \Swoft\Http\Message\Response|\Swoft\Rpc\Server\Response
     * @throws \Swoft\Exception\SwoftException
     */
    protected function sendError($msg = '',$code = ErrorCode::ERROR_UNKNOWN,$data = '')
    {
        return $this->sendJson($data,$code,$msg);
    }
}