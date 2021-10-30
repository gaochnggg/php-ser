<?php

namespace gc\ser\system\engines;


interface EngineInterface
{
    const EV_READ = 1;
    const EV_WRITE = 2;
    // 异常事件
    const EV_EXCEPT = 3;

    const EV_SIGNAL = 4;

    const EV_TIMER = 8;
    const EV_TIMER_ONCE = 16;

    /**
     * @param mixed $fd
     * @param int $flag
     * @param callable $func
     * @param mixed $args
     * @return mixed
     */
    public function add($fd, $flag, $func, $args = null);

    /**
     * @param mixed $fd
     * @param int $flag
     * @return mixed
     */
    public function del($fd, $flag);

    public function loop();
    public function exitLoop();

    public function clearTimer();
    public function clearSignalEvents();
}