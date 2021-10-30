<?php


namespace gc\ser\events\sys;


use gc\ser\system\TcpConnect;

abstract class Tcp
{
    /**
     * @var TcpConnect
     */
    public $tcpConnect;

    public function __construct(TcpConnect $tcpConnect)
    {
        $this->tcpConnect = $tcpConnect;
    }

}