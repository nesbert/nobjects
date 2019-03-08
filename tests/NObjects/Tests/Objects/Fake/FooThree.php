<?php

namespace NObjects\Tests\Objects\Fake;

use NObjects\Nobject;

/**
 * FooThree is a fake testing class.
 */
class FooThree extends Nobject
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
