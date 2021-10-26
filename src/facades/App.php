<?php

namespace gc\ser\facades;

/**
 * @method static string basePath()
 * @method static string configPath()
 * @method static string runPath()
 * @method static string logPath()
 * @method static string runPidPath()
 * @method static void reg(string $name, $call)
 * @method static void singleton(string $name, $call)
 * @method static mixed get(string $name)
 *
 * @see \gc\ser\system\Application
 */
class App extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'app';
    }
}
