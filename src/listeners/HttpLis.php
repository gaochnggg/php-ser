<?php


namespace gc\ser\listeners;


use gc\ser\events\sys\HttpReceiveEvent;
use gc\ser\facades\Safe;
use gc\ser\facades\ServRuntime;

class HttpLis extends TcpLis
{
    public static function receive()
    {
        return function (HttpReceiveEvent $event) {
            Safe::printf('http receive event process:%s fp:%s data:[%s]',
                ServRuntime::getProcessName(),
                (int)$event->tcpConnect->getFp(),
                $event->data
            );
            // 处理 http 请求

        };
    }
}