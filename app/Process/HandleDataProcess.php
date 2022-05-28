<?php
/**
 * Created by PhpStorm.
 * User: night
 * Date: 2021/5/28
 * Time: 18:36
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
 * Class HandleDataProcess
 *
 * @since 2.0
 *
 * @Bean()
 */
class HandleDataProcess extends UserProcess
{
    public function run(Process $process): void
    {
        //$process->name('HandleDataProcess');
        Coroutine::sleep(1*60);//启动时先停60分钟等待其他进程加载数据
        while (true)
        {
            CLog::info('影片数据自动处理进程开启！');
            CollectDataLogic::movieDataDis();
            //CollectDataLogic::syncMovieDataFluxLinkage();
            $time = config('ProcessTime');
            $time = intval($time);
            $time = ($time < 30 || $time>(60*10))?60:$time;
            $time = $time*60;
	    $time = $time/2;//缩短一半时间
            Coroutine::sleep($time);//1小时执行一次
        }
    }
}
