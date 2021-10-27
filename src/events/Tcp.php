<?php


namespace gc\ser\events;


use gc\ser\system\TcpConnect;

abstract class Tcp
{
    /**
     * @var TcpConnect
     */
    protected $tcpConnect;

    public function __construct(TcpConnect $tcpConnect)
    {
        $this->tcpConnect = $tcpConnect;
    }

}