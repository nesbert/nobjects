<?php

namespace NObjects\Tests\Objects\LegacyFake;

use NObjects\Object;

/**
 * BooleanAccessorSameName is a fake testing class.
 */
class BooleanAccessorSameName extends Object
{
    /**
     * @var bool
     */
    protected $isStored;

    /**
     * @param boolean $stored
     *
     * @return $this
     */
    public function setIsStored($stored)
    {
        $this->isStored = $stored;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isStored()
    {
        return $this->isStored;
    }
}
