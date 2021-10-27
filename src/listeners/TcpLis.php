<?php


namespace gc\ser\listeners;


use gc\ser\events\TcpAcceptEvent;
use gc\ser\events\TcpCloseEvent;
use gc\ser\events\TcpReceiveEvent;
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