<?php


namespace gc\ser\system;

use gc\ser\events\sys\TcpAcceptEvent;
use gc\ser\events\sys\WorkerReloadEvent;
use gc\ser\events\sys\WorkerStartEvent;
use gc\ser\events\sys\WorkerStopEvent;
use gc\ser\facades\App;
use gc\ser\facades\Engine;
use gc\ser\facades\EventDispatcher;
use gc\ser\facades\MsgState;
use gc\ser\facades\Safe;
use gc\ser\facades\ServerAttr;
use gc\ser\facades\ServRuntime;
use gc\ser\listeners\SignalLis;
use gc\ser\system\engines\EngineInterface;

class Worker
{
    public function run()
    {
        srand();
        mt_rand();
        // 是否是重启worker
        if (ServRuntime::getServerStatus() == Application::STATUS_RUNNING){
            // 发送worker 重启事件
            EventDispatcher::dispatch(new WorkerReloadEvent());
        }

        // 进程初始化
        $pid = getmypid();
        $workName = sprintf("php-work<%d>", getmypid());
        cli_set_process_title($workName);
        // 设置为 runtime Name 避免系统调用
        ServRuntime::setProcessId($pid);
        ServRuntime::setProcessName($workName);
        ServRuntime::setServerStatus(Application::STATUS_RUNNING);

        SignalLis::regEngHandler();
        // 设置socket
        $serverResource = $this->listen();
        // 添加监听事件
        Engine::add($serverResource, EngineInterface::EV_READ, [$this, 'accept']);
        Engine::add(ServerAttr::getStatTimeOnce(), EngineInterface::EV_TIMER, function (){
            Safe::echo(MsgState::showStatus());
        });

        EventDispatcher::dispatch(new WorkerStartEvent());
        Engine::loop();
        EventDispatcher::dispatch(new WorkerStopEvent());
        exit(0);
    }

    private function listen()
    {
        $ser = stream_socket_server(
            ServRuntime::getAddress(),
            $errNo,
            $errStr,
            STREAM_SERVER_BIND | STREAM_SERVER_LISTEN,
            stream_context_create([
                'socket' =>[
                    'backlog' => 1024,
                    'so_reuseport' => 1,
                ]
            ])
        );
        $socket = socket_import_stream($ser);
        socket_set_option($socket,SOL_TCP,TCP_NODELAY,1);

        stream_set_blocking($ser,0);
        if (!is_resource($ser)){
            throw new \Exception("creat tcp server fail " . ServRuntime::getAddress());
        }
        ServRuntime::setServerSource($ser);
        return $ser;
    }

    public function accept()
    {
        $ser = ServRuntime::getServerSource();
        $fp = stream_socket_accept($ser, -1, $connectParam);

        if (is_resource($fp)){
            MsgState::addConnect();
            list($ip, $port) = explode(":", $connectParam);
            $tcpConnect = new TcpConnect($fp, $ip, $port);
            ServRuntime::setCons($fp, $tcpConnect);
            EventDispatcher::dispatch(new TcpAcceptEvent($tcpConnect));
            return;
        }
        throw new \Exception("conn tcp server fail " . ServRuntime::getAddress());
    }
}