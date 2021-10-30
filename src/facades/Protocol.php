<?php


namespace gc\ser\facades;


/**
 * @method static int|bool Len($data)
 * @method static array encode($data='')
 * @method static int decode($data='')
 * @method static int msgLen($data='')
 *
 * @see \gc\ser\system\protocols\Protocol
 */
class Protocol extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \gc\ser\system\protocols\Protocol::class;
    }
}