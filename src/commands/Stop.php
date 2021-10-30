<?php


namespace gc\ser\commands;

use gc\ser\facades\App;
use gc\ser\facades\Safe;
use SimpleCli\Options\Help;
use SimpleCli\Command;
use SimpleCli\SimpleCli;

class Stop implements Command
{
    use Help;

    public function run(SimpleCli $cli): bool
    {
        $masterPid = file_get_contents(App::runPidPath());
        if ($masterPid && posix_kill($masterPid,0)){
            posix_kill($masterPid,SIGINT);
            Safe::echo("发送了SIGTERM信号了 {$masterPid}");
            $timeout = 5;
            $stopTime = time();
            while (1){
                $masterPidIsAlive = $masterPid&&posix_kill($masterPid,0)&&$masterPid!=posix_getpid();
                if ($masterPidIsAlive){
                    if (time()-$stopTime>=$timeout){
                        Safe::echo("server stop failure");
                        break;
                    }
                    sleep(1);
                    continue;
                }
                Safe::echo("server stop success");
                break;
            }

        }else{
            Safe::echo("server not exist...");
        }
        return true;
    }
}