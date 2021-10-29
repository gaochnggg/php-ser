<?php


namespace gc\ser\listeners;


use gc\ser\events\sys\MasterShutdownEvent;
use gc\ser\events\sys\MasterStartEvent;
use gc\ser\facades\Safe;

class MasterLis
{
    public static function shutdown()
    {
        return function (MasterShutdownEvent $event){
            Safe::echo(MasterShutdownEvent::class . ' happened');
        };
    }

    public static function start()
    {
        return function (MasterStartEvent $event){
            Safe::echo(MasterStartEvent::class . ' happened');
        };
    }

}