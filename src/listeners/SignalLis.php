<?php


namespace gc\ser\listeners;


use gc\ser\facades\App;
use gc\ser\facades\Engine;
use gc\ser\facades\Safe;
use gc\ser\facades\ServRuntime;
use gc\ser\system\Application;
use gc\ser\system\engines\EngineInterface;

class SignalLis
{
    public static function regDefaultHandler()
    {
        pcntl_signal(SIGINT, [SignalLis::class, "sigHandler"], false);
        pcntl_signal(SIGTERM, [SignalLis::class, "sigHandler"], false);
        pcntl_signal(SIGQUIT, [SignalLis::class, "sigHandler"], false);
        pcntl_signal(SIGPIPE, SIG_IGN, false);
    }

    public static function regEngHandler()
    {
        pcntl_signal(SIGINT, SIG_IGN, false);
        pcntl_signal(SIGTERM, SIG_IGN, false);
        pcntl_signal(SIGQUIT, SIG_IGN, false);


        Engine::add(SIGINT, EngineInterface::EV_SIGNAL, [SignalLis::class, 'sigHandler']);
        Engine::add(SIGTERM, EngineInterface::EV_SIGNAL, [SignalLis::class, 'sigHandler']);
        Engine::add(SIGQUIT, EngineInterface::EV_SIGNAL, [SignalLis::class, 'sigHandler']);
    }

    public static function sigHandler($sigNum)
    {
        $masterPid = file_get_contents(App::runPidPath());
        switch ($sigNum) {
            case SIGINT:
            case SIGTERM:
            case SIGQUIT:
                Safe::echo("recv sig {$sigNum}：" . cli_get_process_title());
                //主进程
                if ($masterPid == posix_getpid()) {
                    $pidMap = ServRuntime::getPidMap();
                    foreach ($pidMap as $pid => $pid) {
                        Safe::echo("send to pid:{$pid} sig:{$sigNum} sendStatus:" . posix_kill($pid, $sigNum));
                        ServRuntime::unsetPidMap($pid);
                    }
                    ServRuntime::setServerStatus(Application::STATUS_SHUTDOWN);
                } else {
                    //子进程的 就要停掉现在的任务了
                    Engine::del(ServRuntime::getServerSource(), EngineInterface::EV_READ);

                    $cons = ServRuntime::getCons();
                    if (!empty($cons)){
                        foreach ($cons as $fd => $connection) {
                            $connection->close();
                        }
                        ServRuntime::clearCons();
                    }

                    Engine::clearSignalEvents();
                    Engine::clearTimer();
                    if (Engine::exitLoop()) {
                        Safe::printf("<pid:%d> exit event loop success\r\n", posix_getpid());
                    }

                    fclose(ServRuntime::getServerSource());
                    ServRuntime::setServerSource(null);
                }
                break;
        }
    }
}