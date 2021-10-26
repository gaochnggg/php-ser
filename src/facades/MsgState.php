<?php

namespace gc\ser\facades;


use gc\ser\utils\MsgState as MsgStateUtils;

/**
 * @method static mixed addConnect()
 * @method static mixed removeConnect()
 * @method static mixed addRead()
 * @method static mixed addWrite()
 * @method static mixed showStatus()
 *
 * @see MsgStateUtils
 */
class MsgState extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return MsgStateUtils::class;
    }
}
