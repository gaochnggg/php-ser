<?php


namespace gc\ser\listeners;


use gc\ser\events\sys\TcpAcceptEvent;
use gc\ser\events\sys\TcpCloseEvent;
use gc\ser\events\sys\TcpReceiveEvent;
use gc\ser\facades\Safe;

class TcpLis
{
    public static function accept()
    {
        return function (TcpAcceptEvent $event){
            Safe::echo(TcpAcceptEvent::class . ' happened');
        };
    }

    public static function close()
    {
        return function (TcpCloseEvent $event){
            Safe::echo( TcpCloseEvent::class . ' happened');
        };
    }

    public static function receive()
    {
        return function (TcpReceiveEvent $event){
            Safe::echo( TcpReceiveEvent::class . ' happened');
        };
    }
}