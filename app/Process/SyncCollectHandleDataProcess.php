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
 * Class SyncCollectHandleDataProcess
 *
 * @since 2.0
 *
 * @Bean()
 */
class SyncCollectHandleDataProcess extends UserProcess
{
    public function run(Process $process): void
    {
        //$process->name('SyncCollectHandleData');
	Coroutine::sleep(60*30);//启动时先停30分钟等待其他进程加载数据
        while (true)
        {
            CLog::info('采集数据处理进程开启！');
            CollectDataLogic::handleDataAll();
            $time = config('ProcessTime');
            $time = intval($time);
            $time = ($time < 20 || $time>(60*10))?60:$time;
            $time = $time*60;
            Coroutine::sleep($time);//1小时执行一次
        }
    }
}