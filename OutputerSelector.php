<?php

namespace Test\Import;

include_once 'OutputerConsole.php';

class OutputerSelector
{
    const TYPE_CONSOLE = 'console';

    /**
     * @var array
     */
    private static array $types = [
        self::TYPE_CONSOLE => OutputerConsole::class,
    ];

    /**
     * @param string $outputerType
     *
     * @return OutputerInterface
     */
    public static function select(string $outputerType): OutputerInterface
    {
        $outputerFactory = OutputerSelector::$types[$outputerType];

        return new $outputerFactory();
    }
}
