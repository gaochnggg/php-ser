<?php


namespace gc\ser\events;


use gc\ser\system\TcpConnect;

class TcpReceiveEvent extends Tcp
{
    protected $data;

    public function __construct(TcpConnect $tcpConnect, $data)
    {
        parent::__construct($tcpConnect);
        $this->data = $data;
    }
}