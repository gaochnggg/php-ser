<?php

namespace gc\ser\system\protocols;


interface Protocol
{
    /**
     * 检测数据完整性
     * @param $data
     * @return mixed
     */
    public function Len($data);

    /**
     * 数据封包
     * @param string $data
     * @return mixed
     */
    public function encode($data='');

    /**
     * 数据解包
     * @param string $data
     * @return mixed
     */
    public function decode($data='');

    /**
     * 消息长度
     * @param string $data
     * @return mixed
     */
    public function msgLen($data='');
}