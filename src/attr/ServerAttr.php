<?php


namespace gc\ser\attr;


use Noodlehaus\Config;

class ServerAttr
{
    public $debug;
    public $worker_num;
    public $ip;
    public $port;
    public $protocol;
    public $statTimeOnce;

    public function __construct(Config $config)
    {
        $this->debug = $config->get('debug');
        $this->worker_num = $config->get('worker_num');
        $this->ip = $config->get('ip');
        $this->port = $config->get('port');
        $this->protocol = $config->get('protocol');
        $this->statTimeOnce = $config->get('stat_time_once');
    }

    /**
     * @return mixed
     */
    public function getStatTimeOnce()
    {
        return $this->statTimeOnce;
    }

    /**
     * @return mixed
     */
    public function getDebug()
    {
        return $this->debug;
    }

    /**
     * @param mixed $debug
     */
    public function setDebug($debug): void
    {
        $this->debug = $debug;
    }

    /**
     * @return mixed
     */
    public function getWorkerNum()
    {
        return $this->worker_num;
    }

    /**
     * @param mixed $worker_num
     */
    public function setWorkerNum($worker_num): void
    {
        $this->worker_num = $worker_num;
    }

    /**
     * @return mixed
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @param mixed $ip
     */
    public function setIp($ip): void
    {
        $this->ip = $ip;
    }

    /**
     * @return mixed
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @param mixed $port
     */
    public function setPort($port): void
    {
        $this->port = $port;
    }

    /**
     * @return mixed
     */
    public function getProtocol()
    {
        return $this->protocol;
    }

    /**
     * @param mixed $protocol
     */
    public function setProtocol($protocol): void
    {
        $this->protocol = $protocol;
    }
}