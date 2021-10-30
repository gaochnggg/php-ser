<?php


namespace gc\ser\system;


use gc\ser\events\sys\TcpCloseEvent;
use gc\ser\events\sys\TcpReceiveEvent;
use gc\ser\facades\Engine;
use gc\ser\facades\EventDispatcher;
use gc\ser\facades\MsgState;
use gc\ser\facades\Protocol;
use gc\ser\facades\Safe as SafeFacade;
use gc\ser\facades\ServerAttr;
use gc\ser\facades\ServRuntime;
use gc\ser\system\engines\EngineInterface;

class TcpConnect
{
    const STATUS_CLOSED = 10;
    const STATUS_CONNECTED = 11;
    const HEART_TIME = 10;

    // 接受控制
    private $readBufferSize = 1024;
    private $recvBufferSize = 1024 * 100; // 最大允许接收数据长度
    private $recvLen = 0; // 已接收数据长度
    private $recvBufferFull = 0; // 超出缓冲区次数
    private $recvBuffer = ''; // 缓冲区，存放解析后数据

    // 发送控制
    private $sendLen = 0;
    private $sendBuffer = '';
    private $sendBufferMax = 1024 * 1024;
    private $sendBufferFull = 0; // 超出缓冲区次数


    private $fp;
    private $ip;
    private $port;

    private $connectStatus;
    private $checkHeartTimeId;
    private $heartTime;


    /**
     * TcpConnect constructor.
     * @param $fp
     * @param $ip
     * @param $port
     * @param $engine
     */
    public function __construct($fp, $ip, $port)
    {
        $this->ip = $ip;
        $this->port = $port;

        $this->fp = $fp;
        stream_set_blocking($fp, 0);
        stream_set_write_buffer($fp, 0);
        stream_set_blocking($fp, 0);

        $this->heartTime = time();
        $this->connectStatus = self::STATUS_CONNECTED;

        Engine::add($fp, EngineInterface::EV_READ, [$this, 'receiveMessage']);
        $this->checkHeartTimeId = Engine::add(ServerAttr::getCheckHeartTimeOut(), EngineInterface::EV_TIMER, [$this, 'checkHeartTime']);
    }

    /**
     * 收到消息回调
     */
    public function receiveMessage()
    {
        $data = fread($this->fp, $this->readBufferSize);
        $dataLen = strlen($data);
        if ($data === '' || $data === false || feof($this->fp) || !is_resource($this->fp)) {
            // 对象引用取消
            $this->close();
            return;
        }
        MsgState::addRead();
        // 缓存数据
        $this->recvBuffer .= $data;
        $this->recvLen += $dataLen;

        if ($this->recvLen > 0) {
            // 接受到数据，解析处理
            $this->receiveMessageDo();
        }
    }

    public function sendMessage($data)
    {
        if (!$this->isConnected()) {
            $this->close();
            return false;
        }
        $bin = Protocol::encode($data);
        if ($this->sendLen + $bin[0] < $this->sendBufferMax) {
            $this->sendBuffer .= $bin[1];
            $this->sendLen += $bin[0];
        }
        if ($this->sendLen > $this->sendBufferMax) {
            $this->sendBufferFull++;
        }
        if ($this->sendLen > 0) {
            Engine::add($this->fp, EngineInterface::EV_WRITE, [$this, 'sendMessageDo']);
        }
        return true;
    }

    /**
     * 检测连接心跳
     * @return bool
     */
    public function checkHeartTime()
    {
        $now = time();
        if ($now - $this->heartTime > self::HEART_TIME) {
            SafeFacade::echo(sprintf("心跳时间已经超出:%d\n", $now - $this->heartTime));
            $this->close();
        }
        return true;
    }


    private function receiveMessageDo()
    {
        // 数据循环解析
        while (Protocol::Len($this->recvBuffer) > 0) {
            $msgLen = Protocol::msgLen($this->recvBuffer);
            //截取一条消息
            $oneMsg = substr($this->recvBuffer, 0, $msgLen);
            $decodeDate = Protocol::decode($oneMsg);

            $this->recvBuffer = substr($this->recvBuffer, $msgLen);
            $this->recvLen -= $msgLen;

            // 最后收包的时间
            $this->resetHeartTime();
            // 判断条件 拆包
            EventDispatcher::dispatch(new TcpReceiveEvent($this, $decodeDate));
        }
    }

    public function sendMessageDo()
    {
        while ($this->sendLen > 0 && is_resource($this->fp)) {
            MsgState::addWrite();
            $writeLen = fwrite($this->fp, $this->sendBuffer, $this->sendLen);
            if ($writeLen == $this->sendLen) {
                $this->sendBuffer = '';
                $this->sendLen = 0;
                break;
            } else if ($writeLen > 0) {
                $this->sendBuffer = substr($writeLen, $writeLen);
                $this->sendLen -= $writeLen;
            } else {
                $this->close();
                break;
            }
        }
        Engine::del($this->fp, EngineInterface::EV_WRITE);
    }

    /**
     * 关闭连接
     */
    public function close()
    {
        $this->connectStatus = self::STATUS_CLOSED;
        Engine::del($this->checkHeartTimeId, EngineInterface::EV_TIMER);
        Engine::del($this->fp, EngineInterface::EV_WRITE);
        Engine::del($this->fp, EngineInterface::EV_READ);

        MsgState::removeConnect();
        EventDispatcher::dispatch(new TcpCloseEvent($this));
        ServRuntime::unsetCon($this->fp);

        if (is_resource($this->fp)) {
            fclose($this->fp);
        }
        $this->fp = null;
    }


    public function resetHeartTime()
    {
        $this->heartTime = time();
    }

    public function isConnected()
    {
        return $this->connectStatus == self::STATUS_CONNECTED &&
            is_resource($this->fp);
    }

    /**
     * @return mixed
     */
    public function getFp()
    {
        return $this->fp;
    }

    /**
     * @return mixed
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @return mixed
     */
    public function getPort()
    {
        return $this->port;
    }
}