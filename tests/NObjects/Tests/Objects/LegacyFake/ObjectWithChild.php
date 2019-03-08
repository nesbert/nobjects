<?php

namespace NObjects\Tests\Objects\LegacyFake;

/**
 * ObjectWithChild is a fake object used for testing
 */
class ObjectWithChild extends \NObjects\Object
{
    /**
     * @var ChildObject
     */
    private $childObject;

    /**
     * @var string
     */
    private $foo;

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

    /**
     * @param mixed $childObject
     *
     * @return $this
     */
    public function setChildObject($childObject)
    {
        $this->childObject = $this->_initInstanceObject($childObject, 'NObjects\Tests\Objects\Fake\ChildObject');

        return $this;
    }

    /**
     * @return mixed
     */
    public function getChildObject()
    {
        return $this->childObject;
    }
}
