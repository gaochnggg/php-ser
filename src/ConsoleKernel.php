<?php


namespace gc\ser;

use Exception;
use gc\ser\commands\Start;
use gc\ser\commands\Stop;
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
            'start' => Start::class,
            'stop' => Stop::class
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
        try {
            return $this(...$argv);
        } catch (Exception $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw $e;
        }
    }


}