<?php

namespace gc\ser\facades;

use gc\ser\attr\ServerAttr as ServerAttrConfig;

/**
 * @method static mixed getDebug()
 * @method static mixed getWorkerNum()
 * @method static mixed getIp()
 * @method static mixed getPort()
 * @method static mixed getProtocol()
 *
 * @see \gc\ser\system\Application
 */
class ServerAttr extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return ServerAttrConfig::class;
    }
}
