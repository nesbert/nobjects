<?php

namespace NObjects\Tests\Objects\LegacyFake;

/**
 * FooTwo is a fake testing class.
 */
class FooTwo
{
    private $bars;

    public function __construct(array $bars)
    {
        $this->bars = $bars;
    }

    public function toArray()
    {
        return $this->bars;
    }
}
