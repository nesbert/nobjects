<?php

namespace NObjects\Tests\Objects\Fake;

class FooFour extends \NObjects\Nobject
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
