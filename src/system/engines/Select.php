<?php


namespace gc\ser\system\engines;



class Select implements EngineInterface
{
    public $_allEvents = [];

    public $_readFds = [];
    public $_writeFds = [];
    public $_exptFds = [];
    public $_timeout = 1;

    public $_signalEvents = [];
    public $_eventTimer = [];
    public static $_timerId = 1;
    public $_run=true;

    public function __construct()
    {
    }

    public function timerCallBack()
    {
        foreach ($this->_eventTimer as $k => $param){
            $func = $param[0];
            $runTime = $param[1];//未来执行的时间点
            $flag = $param[2];
            $timerId = $param[3];
            $fd = $param[4];
            $arg = $param[5];

            if ($runTime-time()<=0){

                if ($flag==self::EV_TIMER_ONCE){
                    unset($this->_eventTimer[$timerId]);
                }else{
                    $runTime = time()+$fd;//取得下一个时间点
                    $this->_eventTimer[$k][1] = $runTime;
                }
                call_user_func_array($func,[$arg]);
            }
        }

    }

    public function signalHandler($sigNum)
    {
        $param = $this->_signalEvents[$sigNum];
        if (is_callable($param[0])){
            call_user_func_array($param[0],[$sigNum]);
        }
    }

    public function add($fd, $flag, $func, $args = [])
    {
        switch ($flag){
            case self::EV_SIGNAL:
                $param = [$func, $flag];
                $this->_signalEvents[$fd] = $param;
                if (pcntl_signal($fd, [$this, 'signalHandler'], false)){
                    echo posix_getpid() . " pid 中断信号事件添加成功".PHP_EOL;
                }
                return true;

            case self::EV_TIMER:
            case self::EV_TIMER_ONCE:
                $runTime = time() + $fd;
                $param = [$func, $runTime, $flag, self::$_timerId, $fd, $args];
                $this->_eventTimer[self::$_timerId] = $param;
                return static::$_timerId++;
            case self::EV_READ:
                $fd_key = (int)$fd;
                $this->_readFds[$fd_key] = $fd;
                $this->_allEvents[$fd_key][self::EV_READ] = [$func,[$fd,$flag,$args]];;
                return true;
            case self::EV_WRITE:
                $fd_key = (int)$fd;
                $this->_writeFds[$fd_key] = $fd;
                $this->_allEvents[$fd_key][self::EV_WRITE] = [$func,[$fd,$flag,$args]];;
                return true;
            default:
                return false;
        }
    }


    public function del($fd, $flag)
    {
        switch ($flag){
            case self::EV_SIGNAL:
                $fd_key = (int)$fd;
                if (isset($this->_signalEvents[$fd_key])){
                    pcntl_signal($fd,SIG_IGN, false);
                    unset($this->_signalEvents[$fd_key]);
                }
                break;
            case self::EV_TIMER:
            case self::EV_TIMER_ONCE:
                $fd_key = (int)$fd;
                if (isset($this->_eventTimer[$fd_key])){
                    $this->_eventTimer[$fd_key]->del();
                    unset($this->_eventTimer[$fd_key]);
                }
                break;
            case self::EV_READ:
                $fd_key = (int)$fd;
                if (isset($this->_readFds[$fd_key])){
                    unset($this->_readFds[$fd_key]);
                }
                if (isset($this->_allEvents[$fd_key][self::EV_READ])){
                    unset($this->_allEvents[$fd_key][self::EV_READ]);
                    if (empty($this->_allEvents[$fd_key])){
                        unset($this->_allEvents[$fd_key]);
                    }
                }
                return true;
            case self::EV_WRITE:
                $fd_key = (int)$fd;
                if (isset($this->_writeFds[$fd_key])){
                    unset($this->_writeFds[$fd_key]);
                }
                if (isset($this->_allEvents[$fd_key][self::EV_WRITE])){
                    unset($this->_allEvents[$fd_key][self::EV_WRITE]);
                    if (empty($this->_allEvents[$fd_key])){
                        unset($this->_allEvents[$fd_key]);
                    }
                }
                break;
            default:
                return false;
        }
        return true;
    }

    public function loop()
    {
        echo "select 执行事件循环了 {$this->_run}\r\n";
        while ($this->_run){
            pcntl_signal_dispatch();

            $expFps = $this->_exptFds;
            $writeFps = $this->_writeFds;
            $readFps = $this->_readFds;


            $ret = stream_select($readFps, $writeFps, $expFps, null, null);
            if ($ret===FALSE){
                continue;
            }

            if (!empty($this->_eventTimer)){
                $this->timerCallBack();
            }

            if ($ret){
                if ($readFps){
                    foreach ($readFps as $fp){
                        $fp_key = (int)$fp;
                        if (isset($this->_allEvents[$fp_key][self::EV_READ])){
                            $callBack = $this->_allEvents[$fp_key][self::EV_READ];
                            call_user_func_array($callBack[0], $callBack[1]);
                        }
                    }
                }
                if ($writeFps){
                    foreach ($writeFps as $fp){
                        $fp_key = (int)$fp;
                        if (isset($this->_allEvents[$fp_key][self::EV_WRITE])){
                            $callBack = $this->_allEvents[$fp_key][self::EV_WRITE];
                            call_user_func_array($callBack[0], $callBack[1]);
                        }
                    }
                }
            }
        }
    }

    public function clearTimer()
    {
        $this->_eventTimer = [];
    }

    public function clearSignalEvents()
    {
        foreach ($this->_signalEvents as $fd=>$arg){
            pcntl_signal($fd,SIG_IGN,false);
        }
        $this->_signalEvents = [];
    }

    public function exitLoop()
    {
        $this->_run=false;
        $this->_allEvents=[];
        $this->_readFds=[];
        $this->_writeFds=[];
        $this->_exptFds=[];
        return true;
    }
}