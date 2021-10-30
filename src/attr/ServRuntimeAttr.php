<?php


namespace gc\ser\attr;


use gc\ser\facades\ServerAttr;

class ServRuntimeAttr
{
    public $pidMap = [];
    public $serverStatus;
    public $serverSource;
    public $address;
    public $cons;

    /**
     * @return array
     */
    public function getCons()
    {
        return $this->cons;
    }

    /**
     * @param $fp
     * @param $tcpConnect
     */
    public function setCons($fp, $tcpConnect)
    {
        $this->cons[(int)$fp] = $tcpConnect;
    }

    /**
     * @param $fp
     * @return mixed|null
     */
    public function unsetCon($fp)
    {
        $fpInt = (int)$fp;
        $tcpConnect = null;
        if (isset($this->cons[$fpInt])){
            $tcpConnect = $this->cons[$fpInt];
            unset($this->cons[$fpInt]);
        }
        return $tcpConnect;
    }

    public function clearCons()
    {
        $this->cons = [];
    }

    public function getAddress()
    {
        if (empty($this->address)){
            $this->address = sprintf("tcp://%s:%s", ServerAttr::getIp(), ServerAttr::getPort());
        }
        return $this->address;
    }

    /**
     * @return mixed
     */
    public function getServerSource()
    {
        return $this->serverSource;
    }

    /**
     * @param mixed $serverSource
     */
    public function setServerSource($serverSource): void
    {
        $this->serverSource = $serverSource;
    }

    /**
     * @return array
     */
    public function getPidMap()
    {
        return $this->pidMap;
    }

    /**
     * @param array $pidMap
     */
    public function setPidMap($pid)
    {
        $this->pidMap[$pid] = $pid;
    }

    /**
     * @param array $pidMap
     */
    public function unsetPidMap($pid)
    {
        unset($this->pidMap[$pid]);
    }

    /**
     * @return mixed
     */
    public function getServerStatus()
    {
        return $this->serverStatus;
    }

    /**
     * @param mixed $serverStatus
     */
    public function setServerStatus($serverStatus)
    {
        $this->serverStatus = $serverStatus;
    }
}