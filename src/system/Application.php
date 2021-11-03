<?php
namespace gc\ser\system;

use gc\ser\facades\App;
use Pimple\Container;

class Application extends Container
{
    public const STATUS_STARTING = 1;
    public const STATUS_RUNNING = 2;
    public const STATUS_SHUTDOWN = 3;

    private static $instance;

    protected $publicPath;
    protected $basePath;
    protected $configPath;
    protected $runPath;
    protected $logPath;
    protected $runPidPath;
    protected $tmpPath;

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new static();
            self::$instance->bootstrap();
        }
        return self::$instance;
    }

    private function bootstrap()
    {
        $this->basePath = __DIR__ . '/../..';
        $this->publicPath = $this->basePath .DIRECTORY_SEPARATOR. "public";
        $this->configPath = $this->basePath .DIRECTORY_SEPARATOR. "config";
        $this->logPath = $this->basePath .DIRECTORY_SEPARATOR. "storage".DIRECTORY_SEPARATOR."log";
        $this->tmpPath = $this->basePath .DIRECTORY_SEPARATOR. "storage".DIRECTORY_SEPARATOR."tmp";
        $this->runPath = $this->basePath .DIRECTORY_SEPARATOR. "storage".DIRECTORY_SEPARATOR."run";
        $this->runPidPath = $this->basePath .DIRECTORY_SEPARATOR. "storage".DIRECTORY_SEPARATOR."run" . DIRECTORY_SEPARATOR . "pid.log";

        $this->reg('app', $this);
        $this->reg('path.public', $this->publicPath);
        $this->reg('path.base', $this->basePath);
        $this->reg('path.config', $this->configPath);
        $this->reg('path.run', $this->runPath);
        $this->reg('path.tmp', $this->tmpPath);
        $this->reg('path.log', $this->logPath);
        $this->reg('path.run.pid', $this->runPidPath);

        $this->singleton(Safe::class, function (){
            return new Safe();
        });
    }

    /**
     * @param string $name
     * @param $call
     */
    public function reg(string $name, $call)
    {
        $this[$name] = $call;
    }

    /**
     * @param string $name
     * @param $call
     */
    public function singleton(string $name, $call)
    {
        $this[$name] = $this->factory($call);
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function get(string $name)
    {
        return $this[$name];
    }

    /**
     * @return string
     */
    public function basePath()
    {
        return $this->basePath;
    }

    /**
     * @return string
     */
    public function configPath()
    {
        return $this->configPath;
    }

    /**
     * @return string
     */
    public function runPath()
    {
        return $this->runPath;
    }

    /**
     * @return string
     */
    public function logPath()
    {
        return $this->logPath;
    }

    /**
     * @return string
     */
    public function runPidPath()
    {
        return $this->runPidPath;
    }

    /**
     * @return string
     */
    public function publicPath()
    {
        return $this->publicPath;
    }

    /**
     * @return string
     */
    public function tmpPath()
    {
        return $this->tmpPath;
    }

    public function netReceiveClass()
    {
        return App::get("net.receive");
    }
}