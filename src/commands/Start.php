<?php


namespace gc\ser\commands;

use gc\ser\events\sys\MasterShutdownEvent;
use gc\ser\facades\App;
use gc\ser\facades\EventDispatcher;
use gc\ser\facades\Safe;
use gc\ser\facades\ServerAttr;
use gc\ser\facades\ServRuntime;
use gc\ser\listeners\Listeners;
use gc\ser\listeners\SignalLis;
use gc\ser\system\Application;
use gc\ser\system\Worker;
use SimpleCli\Options\Help;
use SimpleCli\Command;
use SimpleCli\SimpleCli;

class Start implements Command
{
    use Help;

    public function run(SimpleCli $cli): bool
    {
        // 设置服务状态
        ServRuntime::setServerStatus(Application::STATUS_STARTING);
        // 注册默认事件处理
        Listeners::sysRegister();

        // 注册系统事件处理函数
        SignalLis::regDefaultHandler();
        // 检测是否有服务在运行
        $this->checkMasterStatus();
        // 保存 主进程id信息
        $this->saveMasterPid();
        // 启动work
        $this->forkWorker();
        // 设置服务状态
        ServRuntime::setServerStatus(Application::STATUS_RUNNING);

        // 设置master 进程
        $this->masterWorker();

        return true;
    }

    private function checkMasterStatus()
    {
        if (file_exists(App::runPidPath())) {
            $masterPid = file_get_contents(App::runPidPath());
        } else {
            $masterPid = 0;
        }

        // 验证当前进程是否存在
        if (
            $masterPid &&
            posix_kill($masterPid, 0) &&
            $masterPid != posix_getpid()
        ) {
            exit("server already running...");
        } else if ($masterPid > 0) {
            unlink(App::runPidPath());
        }
    }

    private function saveMasterPid()
    {
        $masterPid = posix_getpid();
        file_put_contents(App::runPidPath(), $masterPid);
    }

    private function forkWorker()
    {
        $workerNum = ServerAttr::getWorkerNum();
        if (empty($workerNum)) {
            $workerNum = 1;
        }
        for ($i = 0; $i < $workerNum; $i++) {
            $pid = pcntl_fork();
            if ($pid === 0) {
                sleep(1);
                (new Worker())->run();
            } else {
                ServRuntime::setPidMap($pid);
            }
        }
    }

    private function masterWorker()
    {
        $addr = ServRuntime::getAddress();
        $pid = getmypid();
        $masterName = sprintf("php-master<%d>", getmypid());
        cli_set_process_title($masterName);
        Safe::echo("{$masterName}  {$addr}  start");

        ServRuntime::setProcessId($pid);
        ServRuntime::setProcessName($masterName);

        while (1){
            pcntl_signal_dispatch();
            // 子进程是否退出，回收资源
            $pid = pcntl_wait($status);
            pcntl_signal_dispatch();
            if ($pid>0){
                ServRuntime::unsetPidMap($pid);
                // 如果服务未停止 则重启进程
                if (Application::STATUS_SHUTDOWN != ServRuntime::getServerStatus()){
                    $this->reloadWorker();
                }
            }
            if (empty(ServRuntime::getPidMap())){
                break;
            }
        }
        EventDispatcher::dispatch(new MasterShutdownEvent());
        exit(0);
    }

    public function reloadWorker()
    {
        Safe::echo("reload worker  start");
        $pid = pcntl_fork();
        if ($pid===0){
            (new Worker())->run();
        }
        else{
            ServRuntime::setPidMap($pid);
        }
    }
}