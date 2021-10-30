<?php


namespace gc\ser\system;


use gc\ser\facades\App;
use gc\ser\facades\ServerAttr;

class Safe
{
    public function echo($msg)
    {
        $msg = "[".date("Y-m-d H:i:s")."]: " . $msg . PHP_EOL;
        if (ServerAttr::getDaemon() == true){
            $logPath = App::get("path.log");
            $fileName = $logPath . DIRECTORY_SEPARATOR . "log-".date('Y-m-d') . '.log';
            file_put_contents($fileName, $msg, FILE_APPEND);
        }else{
            echo $msg;
        }
    }

    public function printf($format, ...$data)
    {
        $this->echo(sprintf($format, ...$data));
    }
}