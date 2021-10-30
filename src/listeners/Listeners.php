<?php


namespace gc\ser\listeners;


use gc\ser\events\sys\MasterShutdownEvent;
use gc\ser\events\sys\MasterStartEvent;
use gc\ser\events\sys\TcpAcceptEvent;
use gc\ser\events\sys\TcpCloseEvent;
use gc\ser\events\sys\TcpReceiveEvent;
use gc\ser\events\sys\WorkerReloadEvent;
use gc\ser\events\sys\WorkerStartEvent;
use gc\ser\events\sys\WorkerStopEvent;
use gc\ser\facades\EventDispatcher;

class Listeners
{
    public static function sysRegister()
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