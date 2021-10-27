<?php


namespace gc\ser;

use Exception;
use gc\ser\commands\Start;
use gc\ser\listeners\Listeners;
use SimpleCli\SimpleCli;

class ConsoleKernel extends SimpleCli
{
    public function getVersion(): string
    {
        return '0.0.1';
    }

    public function getCommands() : array
    {
        return [
            'start' => Start::class
        ]; // Your class needs to implement the getCommands(), we'll see later what to put in here.
    }

    /**
     * Run the console application.
     *
     * @param $argv
     * @return int
     * @throws \Throwable
     */
    public function handle($argv)
    {
        // 注册默认事件处理
        Listeners::register();

        try {
            return $this(...$argv);
        } catch (Exception $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw $e;
        }
    }


}