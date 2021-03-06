<?php

namespace NObjects\Tests\Objects\LegacyFake;

use NObjects\Object;

/**
 * FooThree is a fake testing class.
 */
class FooThree extends Object
{
    private $bar;

    public function setBar($bar)
    {
        $this->bar = $bar;

        return $this;
    }

    public function getBar()
    {
        return $this->bar;
    }
}
