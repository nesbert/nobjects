<?php

namespace NObjects\Tests\Objects\LegacyFake;

/**
 * ChildObject is a fake object used for testing
 */
class ChildObject extends \NObjects\Object
{
    /**
     * @var string
     */
    private $foo;

    /**
     * @var string
     */
    private $bar;

    /**
     * @var string
     */
    private $baz;

    /**
     * @param string $bar
     *
     * @return $this
     */
    public function setBar($bar)
    {
        $this->bar = $bar;
        return $this;
    }

    /**
     * @return string
     */
    public function getBar()
    {
        return $this->bar;
    }

    /**
     * @param string $baz
     *
     * @return $this
     */
    public function setBaz($baz)
    {
        $this->baz = $baz;
        return $this;
    }

    /**
     * @return string
     */
    public function getBaz()
    {
        return $this->baz;
    }

    /**
     * @param string $foo
     *
     * @return $this
     */
    public function setFoo($foo)
    {
        $this->foo = $foo;
        return $this;
    }

    /**
     * @return string
     */
    public function getFoo()
    {
        return $this->foo;
    }
}
