<?php
/**
 * Created by PhpStorm.
 * User: night
 * Date: 2021/5/17
 * Time: 15:09
 */

namespace App\Process;

use App\Model\Logic\CollectDataLogic;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Annotation\Mapping\Inject;
use Swoft\Db\Exception\DbException;
use Swoft\Log\Helper\CLog;
use Swoft\Process\Process;
use Swoft\Process\UserProcess;
use Swoole\Coroutine;

/**
 * Class SynchronouslyCollectDataProcess
 *
 * @since 2.0
 *
 * @Bean()
 */
class SynchronouslyCollectDataProcess extends UserProcess
{
    public function run(Process $process): void
    {
        //$process->name('SynchronouslyCollectData');
        $index = 0;
        while (true)
        {
            CLog::info('采集数据同步进程开启！');
            if($index == 0)//每执行10次同步一次属性数据【其实这个不需要同步感觉】
            {
                CollectDataLogic::syncAttributesData();
                $index = 0;
            }
            CollectDataLogic::syncMovieData();
            $index++;
            $time = config('CollectProcessTime');
            $time = intval($time);
            $time = ($time < 30 || $time>(60*10))?60:$time;
            $time = $time*60;
            Coroutine::sleep($time);//1小时执行一次
        }
    }
}