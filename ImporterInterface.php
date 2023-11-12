<?php

namespace Test\Import;

interface ImporterInterface
{
    /**
     * @return $this
     */
    public function prepare(): self;

    /**
     * @return $this
     */
    public function validate(): self;

    /**
     * @return $this
     */
    public function run(): self;
}