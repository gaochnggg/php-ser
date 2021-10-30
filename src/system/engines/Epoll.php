<?php


namespace gc\ser\system\engines;



use gc\ser\facades\Safe;
use gc\ser\facades\ServRuntime;

class Epoll implements EngineInterface
{
    protected $_eventBase = null;
    public $_allEvents = [];
    public $_signalEvents = [];
    public $_eventTimer = [];
    public static $_timerId = 1;

    public function __construct()
    {
        $this->_eventBase = new \EventBase();
    }

    public function timerCallback($fd, $what, $args)
    {
        $func = $args[0];
        $params = $args[1];
        $flag = $args[2];
        $timerId = $args[3];
        if ($flag == self::EV_TIMER_ONCE){
            $event = $this->_eventTimer[$timerId];
            $event->del();
            unset($this->_eventTimer[$timerId]);
        }

        call_user_func_array($func, $params);
    }

    public function add($fd, $flag, $func, $args = [])
    {
        switch ($flag){
            case self::EV_SIGNAL:
                $fd_key = (int)$fd;
                $event = new \Event($this->_eventBase, $fd,\Event::SIGNAL, $func, $args);
                if (!$event->add()){
                    return false;
                }
                $this->_signalEvents[$fd_key] = $event;
                return true;
            case self::EV_TIMER:
            case self::EV_TIMER_ONCE:
                $param = array($func, (array)$args, $flag, self::$_timerId);
                $event = new \Event($this->_eventBase, -1, \Event::TIMEOUT|\Event::PERSIST, array($this, "timerCallback"), $param);
                if (!$event||!$event->addTimer($fd)) {
                    return false;
                }
                $this->_eventTimer[self::$_timerId] = $event;
                return self::$_timerId++;
            case self::EV_READ:
            case self::EV_WRITE:
                $fd_key = (int)$fd;
                $realFlag = ($flag === self::EV_READ ? \Event::READ : \Event::WRITE) | \Event::PERSIST;
                $event = new \Event($this->_eventBase, $fd, $realFlag, $func, $args);
                if (!$event->add()){
                    return false;
                }
                $this->_allEvents[$fd_key][$flag] = $event;
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
                    if ($this->_signalEvents[$fd_key]->del()){
//                        echo "epoll signal del ok" .PHP_EOL;
                    }
                    unset($this->_signalEvents[$fd_key]);
                }
                break;
            case self::EV_TIMER:
            case self::EV_TIMER_ONCE:
                $fd_key = (int)$fd;
                if (isset($this->_eventTimer[$fd_key])){
                    $this->_eventTimer[$fd_key]->del();
                    unset($this->_eventTimer[$fd_key]);
//                    echo "epoll _eventTimer del ok" .PHP_EOL;
                }
                break;
            case self::EV_READ:
            case self::EV_WRITE:
                $fd_key = (int)$fd;
                if (isset($this->_allEvents[$fd_key][$flag])){
                    $this->_allEvents[$fd_key][$flag]->del();
                    unset($this->_allEvents[$fd_key][$flag]);
//                    echo sprintf("epoll  %s del ok" .PHP_EOL, $flag == self::EV_READ ? "read" : "write");
                }
                if (empty($this->_allEvents[$fd_key])){
                    unset($this->_allEvents[$fd_key]);
                }
                break;
            default:
                var_dump(__FILE__ . '  ' . __LINE__);
                return false;
        }
        return true;
    }


    public function loop()
    {
        echo "epoll loop start \r\n";
        return $this->_eventBase->loop();
    }

    public function exitLoop()
    {
        return $this->_eventBase->stop();
    }


    public function clearTimer()
    {
        foreach ($this->_eventTimer as $timerId=>$event){
            if($event->del()){
                Safe::printf("process:%s epoll 移除定时事件成功", ServRuntime::getProcessName());
            }
        }
        $this->_eventTimer = [];
    }

    public function clearSignalEvents()
    {
        foreach ($this->_signalEvents as $fd => $event){
            if($event->del()){
                Safe::printf("process:%s epoll 移除signal成功", ServRuntime::getProcessName());
            }
        }
        $this->_signalEvents = [];
    }
}