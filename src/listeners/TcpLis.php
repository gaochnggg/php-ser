<?php


namespace gc\ser\listeners;


use gc\ser\events\sys\TcpAcceptEvent;
use gc\ser\events\sys\TcpCloseEvent;
use gc\ser\events\sys\TcpReceiveEvent;
use gc\ser\facades\Safe;
use gc\ser\facades\ServRuntime;

class TcpLis
{
    public static function accept()
    {
        return function (TcpAcceptEvent $event){
            Safe::printf(
                'tcp accept event process:%s fp:%d',
                ServRuntime::getProcessName(),
                (int)$event->tcpConnect->getFp()
            );
            $event->tcpConnect->sendMessage("aaaa");
        };
    }

    public static function close()
    {
        return function (TcpCloseEvent $event){
            Safe::printf(
                'tcp close event process:%s fp:%d',
                ServRuntime::getProcessName(),
                (int)$event->tcpConnect->getFp()
            );
        };
    }

    public static function receive()
    {
        return function (TcpReceiveEvent $event){
            Safe::printf('tcp receive event process:%s fp:%s data:[%s]',
                ServRuntime::getProcessName(),
                (int)$event->tcpConnect->getFp(),
            $event->data
            );
        };
    }
}