<?php

namespace gc\ser\facades;

use gc\ser\attr\ServRuntimeAttr;

/**
 * @method static array getPidMap()
 * @method static mixed setPidMap($pid)
 * @method static mixed unsetPidMap($pid)
 * @method static int getServerStatus()
 * @method static mixed setServerStatus($status)
 * @method static mixed getAddress()
 * @method static mixed getServerSource()
 * @method static mixed setServerSource($serverSource)
 * @method static array getCons()
 * @method static mixed setCons($fp, $tcpConnect)
 * @method static mixed unsetCon($fp)
 * @method static mixed clearCons()
 * @see ServerAttrConfig
 */
class ServRuntime extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return ServRuntimeAttr::class;
    }
}
