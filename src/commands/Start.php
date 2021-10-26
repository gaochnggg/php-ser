<?php


namespace gc\ser\commands;

use gc\ser\facades\App;
use gc\ser\facades\Safe;
use SimpleCli\Options\Help;
use SimpleCli\Command;
use SimpleCli\SimpleCli;

class Start implements Command
{
    use Help;

    public function run(SimpleCli $cli): bool
    {
        $this->init();


        return true;
    }

    private function init()
    {
        if (file_exists(App::runPidPath())){
            $masterPid = file_get_contents(App::runPidPath());
        }else{
            $masterPid = 0;
        }

        // 验证当前进程是否存在
        if (
            $masterPid &&
            posix_kill($masterPid,0) &&
            $masterPid != posix_getpid()
        ){
            exit("server already running...");
        }else{
            unlink(App::runPidPath());
        }

    }
}