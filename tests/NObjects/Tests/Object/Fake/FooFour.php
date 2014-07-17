<?php

namespace NObjects\Tests\Object\Fake;

class FooFour extends \NObjects\Object
{
    public $bar;

    public function setBar($bar)
    {
        $this->bar = $bar;
        return $this;
    }


    /**
     * @return mixed
     */
    public function getBar()
    {
        return $this->bar;
    }
}
