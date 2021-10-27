<?php


namespace gc\ser\commands;

use gc\ser\facades\App;
use SimpleCli\Options\Help;
use SimpleCli\Command;
use SimpleCli\SimpleCli;

class Start implements Command
{
    use Help;

    public function run(SimpleCli $cli): bool
    {
        // 检测是否有服务在运行
        $this->checkMasterStatus();
        // 保存 主进程id信息
        $this->saveMasterPid();

        return true;
    }

    private function checkMasterStatus()
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
        }else if ($masterPid > 0){
            unlink(App::runPidPath());
        }
    }

    private function saveMasterPid()
    {
        $masterPid = posix_getpid();
        file_put_contents(App::runPidPath(), $masterPid);
    }
}