<?php
/**
 * Created by PhpStorm.
 * User: night
 * Date: 2021/5/17
 * Time: 16:46
 */

namespace App\Http\Common;


class Common
{

    /**
     * 发送HTTP数据包
     * @param string $url
     * @param array $data
     * @return bool|mixed
     */
    public static function sendHttpData(string $url, array $data)
    {
        if (empty($url))
        {
            return false;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $return = curl_exec($ch);
        curl_close($ch);
        return $return;
    }

    /**
     * 判断字符串是否是时间格式
     * @param $dateTime
     * @return false|int|null
     */
    public static function  isDateTime($dateTime)
    {
        $ret = strtotime($dateTime);
        return ($ret !== FALSE || $ret != -1)?date('Y-m-d H:i:s',$ret):NULL;
    }

    /**
     * 判断字符串是否存在日语
     * @param $dateTime
     * @return false|int|null
     */
    public static function  isJapanese($s)
    {
        if(preg_match_all('/([\x{0800}-\x{4e00}]+)/u',$s,$m)) {
            if(count($m)>0)
            {
                if(count($m[1]??[])>0)
                {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * 根据路径创建指定文件夹
     * @param string $path
     */
    public static function createDir($path = '/')
    {
        $pathArr = explode("/", $path);
        $pathed="";
        foreach ($pathArr as $key=>$row)
        {
            $pathed = $pathed.$row."/";
            if (is_dir($pathed))
            {
                continue;
            }
            else
            {
                mkdir(iconv("UTF-8", "GBK", $pathed), 0777, true);
            }
        }
    }
}