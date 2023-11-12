<?php

namespace Test\Import;

include_once 'OutputerInterface.php';
include_once 'ConsoleNotificateEvent.php';

use Test\Import\ConsoleNotificateEvent as Event;

class OutputerConsole implements OutputerInterface
{
    const DISPLAY_LIMIT = 2000;

    /**
     * @param string $path
     */
    public function __construct(
        private string $path = 'file.txt',
    ) {
    }

    /**
     * @return $this
     */
    public function prepare(): self
    {
        Event::listen(Event::END_SEARCH, function() {
            echo "\nDone. \n";
        });

        Event::listen(Event::LIMIT, function() {
            echo sprintf("First %d displayed. \n", self::DISPLAY_LIMIT);
        });

        return $this;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return $this
     */
    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return json_decode(file_get_contents($this->path));
    }

    /**
     * @return mixed
     */
    public function output(): mixed
    {
        return call_user_func(function() {
            echo "\n Results: " . Importer::getCountResults() . "\n";

            foreach ($this->getData() as $key => $item) {
                echo $key . ". " . json_encode($item, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
            }

            Event::trigger(Event::END_SEARCH);

            if (Importer::getCountResults() > self::DISPLAY_LIMIT) {
                Event::trigger(Event::LIMIT);
            }
        });
    }
}