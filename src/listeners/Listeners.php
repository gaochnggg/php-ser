<?php


namespace gc\ser\listeners;


use gc\ser\events\MasterShutdownEvent;
use gc\ser\events\MasterStartEvent;
use gc\ser\events\TcpAcceptEvent;
use gc\ser\events\TcpCloseEvent;
use gc\ser\events\TcpReceiveEvent;
use gc\ser\events\WorkerReloadEvent;
use gc\ser\events\WorkerStartEvent;
use gc\ser\events\WorkerStopEvent;
use gc\ser\facades\EventDispatcher;

class Listeners
{
    public static function register()
    {
        $list = [
            WorkerStartEvent::class => WorkerLis::start(),
            WorkerStopEvent::class => WorkerLis::Stop(),
            WorkerReloadEvent::class => WorkerLis::Reload(),

            MasterStartEvent::class => MasterLis::start(),
            MasterShutdownEvent::class => MasterLis::shutdown(),

            TcpAcceptEvent::class => TcpLis::accept(),
            TcpCloseEvent::class => TcpLis::close(),
            TcpReceiveEvent::class => TcpLis::receive(),
        ];

        foreach ($list as $k => $call){
            EventDispatcher::subscribeTo($k, $call);
        }
    }
}