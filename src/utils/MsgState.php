<?php

namespace gc\ser\utils;


class MsgState
{
    private $beginTime;
    private $size;

    private $conNum = 0;
    private $readNum = 0;
    private $writeNum = 0;

    public function __construct($size = 10)
    {
        $this->size = $size;
        $this->beginTime = time();
    }

    public function addConnect()
    {
        $this->conNum++;
    }

    public function removeConnect()
    {
        $this->conNum--;
    }

    public function addRead()
    {
        $this->readNum++;
    }

    public function addWrite()
    {
        $this->writeNum++;
    }

    public function showStatus()
    {
        $pid = getmypid();
        $data = date('Y-m-d h:i:s', $this->beginTime);
        $statStr = "pid:{$pid}  {$data} conNum:{$this->conNum} read:{$this->readNum} write:{$this->writeNum}";
        $this->beginTime = time();
        $this->readNum = 0;
        $this->writeNum = 0;

        return $statStr;
    }

}