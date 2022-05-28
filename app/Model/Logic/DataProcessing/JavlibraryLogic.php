<?php
/**
 * Created by PhpStorm.
 * User: night
 * Date: 2021/5/19
 * Time: 16:03
 */

namespace App\Model\Logic\DataProcessing;


class JavlibraryLogic extends DataProcessingBaseLogic
{

    /**
     * 积分处理
     * @param $score
     */
    public function  getScoreDis($score)
    {
        return floatval($score);
    }
}