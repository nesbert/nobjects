<?php

namespace NObjects\Tests\Objects\Fake;

use NObjects\Nobject;

/**
 * FooOne is a fake testing class.
 */
class FooOne extends Nobject
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
