<?php

namespace gc\ser\listeners;

use gc\ser\events\sys\WorkerReloadEvent;
use gc\ser\events\sys\WorkerStartEvent;
use gc\ser\events\sys\WorkerStopEvent;
use gc\ser\facades\Safe;

class WorkerLis
{
    public static function stop()
    {
        return function (WorkerStopEvent $event){
            Safe::echo(WorkerStopEvent::class . ' happened');
        };
    }

    public static function start()
    {
        return function (WorkerStartEvent $event){
            Safe::echo(WorkerStartEvent::class . ' happened');
        };
    }

    public static function reload()
    {
        return function (WorkerReloadEvent $event){
            Safe::echo(WorkerReloadEvent::class . ' happened');
        };
    }
}