<?php


namespace gc\ser\listeners;


use gc\ser\attr\ServRuntimeAttr;
use gc\ser\events\sys\MasterShutdownEvent;
use gc\ser\events\sys\MasterStartEvent;
use gc\ser\facades\Safe;
use gc\ser\facades\ServRuntime;

class MasterLis
{
    public static function shutdown()
    {
        return function (MasterShutdownEvent $event){
            Safe::printf("process:%s shutdown", ServRuntime::getProcessName());
        };
    }

    public static function start()
    {
        return function (MasterStartEvent $event){
            Safe::printf("process:%s start", ServRuntime::getProcessName());
        };
    }

}