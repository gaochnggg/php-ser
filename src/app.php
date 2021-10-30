<?php

// 创建容器
use gc\ser\attr\ServerAttr;
use gc\ser\attr\ServRuntimeAttr;
use gc\ser\ConsoleKernel;
use gc\ser\facades\Facade;
use gc\ser\facades\ServerAttr as ServerAttrFacades;
use gc\ser\System\Application;
use gc\ser\system\engines\EngineInterface;
use gc\ser\system\engines\Epoll;
use gc\ser\system\protocols\Protocol;
use gc\ser\system\protocols\Text;
use gc\ser\utils\MsgState;
use League\Event\EventDispatcher;
use Noodlehaus\Config;
use gc\ser\facades\App;

Facade::setFacadeApplication(Application::getInstance());

// 注册核心服务
App::singleton(ConsoleKernel::class, function (){
    return new ConsoleKernel();
});

// 注册基础配置
App::singleton(ServerAttr::class, function (){
    $config = Config::load(App::get("path.config") .DIRECTORY_SEPARATOR. 'ser.json');
    return new ServerAttr($config);
});

// 注册运行时配置
App::singleton(ServRuntimeAttr::class, function (){
    return new ServRuntimeAttr();
});

// 注册事件处理
App::singleton(EventDispatcher::class, function (){
    return new EventDispatcher();
});

// 注册统计服务
App::singleton(MsgState::class, function (){
    return new MsgState(ServerAttrFacades::getStatTimeOnce());
});

// 注册事件引擎
App::singleton(EngineInterface::class, function (){
    $engin = ServerAttrFacades::getEngine();
    if ($engin == 'epoll'){
        return new Epoll();
    }
    throw new Exception("EngineInterface err");
});


// 注册应用层协议
App::singleton(Protocol::class, function (){
//    $protocol = ServerAttrFacades::getProtocol();
    return new Text();
    if ($protocol == 'text'){
        return new Text();
    }
    throw new Exception("Protocol err");
});
