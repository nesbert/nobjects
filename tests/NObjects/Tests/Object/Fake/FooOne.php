<?php

namespace NObjects\Tests\Object\Fake;

use NObjects\Object;

/**
 * FooOne is a fake testing class.
 */
class FooOne extends Object
{
    private $bars;
    private $_skip;

    public function setBars(\ArrayObject $bar)
    {
        $this->bars = $bar;
    }

    public function getBars()
    {
        return $this->bars;
    }

    public function setSkip($skip)
    {
        $this->_skip = $skip;
    }

    public function getSkip()
    {
        return $this->_skip;
    }
}
