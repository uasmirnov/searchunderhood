<?php

namespace Test\Import;

interface WriterInterface
{
    /**
     * @return $this
     */
    public function prepare(): self;

    /**
     * @return bool
     */
    public function save(): bool;
}