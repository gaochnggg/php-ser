<?php


namespace gc\ser\facades;

/**
 * @method static void echo(string $name)
 * @method static void printf(string $format, ...$data)
 *
 * @see \gc\ser\system\Safe
 */
class Safe extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \gc\ser\system\Safe::class;
    }
}