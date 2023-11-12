<?php

namespace Test\Import;

class ConsoleNotificateEvent
{
    const START_SEARCH = 'start_search';
    const END_SEARCH = 'end_search';
    const LIMIT = 'limit';

    /**
     * @var array
     */
    private static array $events = [];

    /**
     * @param string   $name
     * @param callable $callback
     *
     * @return void
     */
    public static function listen(string $name, callable $callback) : void
    {
        self::$events[$name][] = $callback;
    }

    /**
     * @param string $name
     *
     * @return void
     */
    public static function trigger(string $name): void
    {
        foreach (self::$events[$name] as $event => $callback) {
            call_user_func($callback);
        }
    }
}