<?php

namespace NObjects\Tests\Objects\Fake;

use NObjects\Nobject;

/**
 * BooleanAccessorDifferentName is a fake testing class.
 */
class BooleanAccessorDifferentName extends Nobject
{
    /**
     * @var bool
     */
    protected $stored;

    /**
     * @param boolean $stored
     *
     * @return $this
     */
    public function setStored($stored)
    {
        $this->stored = $stored;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isStored()
    {
        return $this->stored;
    }
}
