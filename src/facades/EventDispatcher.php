<?php

namespace gc\ser\facades;

use League\Event\ListenerPriority;

/**
 * @method static void subscribeTo(string $event, callable $listener, int $priority = ListenerPriority::NORMAL)
 * @method static object dispatch(object $event)
 *
 * @see \League\Event\EventDispatcher
 */
class EventDispatcher extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \League\Event\EventDispatcher::class;
    }
}
