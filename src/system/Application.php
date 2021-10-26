<?php
namespace gc\ser\system;

use Pimple\Container;

class Application extends Container
{
    private static $instance;

    protected $basePath;
    protected $configPath;
    protected $runPath;
    protected $logPath;
    protected $runPidPath;

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
        $this->configPath = $this->basePath .DIRECTORY_SEPARATOR. "config";
        $this->runPath = $this->basePath .DIRECTORY_SEPARATOR. "storage".DIRECTORY_SEPARATOR."run";
        $this->logPath = $this->basePath .DIRECTORY_SEPARATOR. "storage".DIRECTORY_SEPARATOR."log";

        $this->reg('app', $this);
        $this->reg('path.base', $this->basePath);
        $this->reg('path.config', $this->configPath);
        $this->reg('path.run', $this->runPath);
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

}