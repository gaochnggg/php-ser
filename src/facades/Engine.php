<?php


namespace gc\ser\facades;


use gc\ser\system\engines\EngineInterface;

/**
 * @method static mixed add($fd, $flag, $func, $args = null);
 * @method static mixed del($fd, $flag);
 * @method static mixed loop();
 * @method static mixed exitLoop();
 * @method static mixed clearTimer();
 * @method static mixed clearSignalEvents();
 *
 * @see \gc\ser\system\engines\EngineInterface
 */
class Engine extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return EngineInterface::class;
    }
}