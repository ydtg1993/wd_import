<?php
/**
 * Created by PhpStorm.
 * User: night
 * Date: 2021/5/19
 * Time: 16:03
 */

namespace App\Model\Logic\DataProcessing;


class Fc2Logic extends DataProcessingBaseLogic
{

    /**
     * 这个是时分秒
     * @param $videoTime
     * @return float|int|mixed|null
     */
    public function getVideoTime($videoTime)
    {
        if($videoTime == null)
        {
            return null;
        }
        $temp = explode(':',$videoTime);
        if(is_array($temp))
        {
            $index = 0;
            if($index  >= 3)
            {
               return (((($temp[0]??0)*60*60)+(($temp[1]??0)*60) + ($temp[2]??0)));
            }
            else if($index  == 2)
            {
                return ((($temp[0]??0)*60) + ($temp[1]??0));
            }
            else if($index < 2)
            {
                return ($temp[0]??0);
            }

        }

        return self::getStrNum($videoTime);
    }
}