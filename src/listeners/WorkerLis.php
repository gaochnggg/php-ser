<?php

namespace gc\ser\listeners;

use gc\ser\events\sys\WorkerReloadEvent;
use gc\ser\events\sys\WorkerStartEvent;
use gc\ser\events\sys\WorkerStopEvent;
use gc\ser\facades\Safe;
use gc\ser\facades\ServRuntime;

class WorkerLis
{
    public static function stop()
    {
        return function (WorkerStopEvent $event){
            Safe::printf('process:%s stop event', ServRuntime::getProcessName());
        };
    }

    public static function start()
    {
        return function (WorkerStartEvent $event){
            Safe::printf('process:%s start event', ServRuntime::getProcessName());
        };
    }

    public static function reload()
    {
        return function (WorkerReloadEvent $event){
            Safe::printf('process:%s reload event', ServRuntime::getProcessName());
        };
    }
}