<?php

namespace NObjects\Tests\Objects\Fake;

use NObjects\Nobject;

/**
 * BooleanAccessorSameName is a fake testing class.
 */
class BooleanAccessorSameName extends Nobject
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
