<?php

namespace Test\Import;

interface OutputerInterface
{
    /**
     * @return $this
     */
    public function prepare(): self;

    /**
     * @return mixed
     */
    public function output(): mixed;
}