<?php

namespace NObjects\Tests\Objects\LegacyFake;

use NObjects\Object;

/**
 * BooleanAccessorDifferentNameWithHas is a fake testing object.
 */
class BooleanAccessorDifferentNameWithHas extends Object
{
    /**
     * @var bool
     */
    protected $availableSeating;

    /**
     * @param boolean $availableSeating
     *
     * @return $this
     */
    public function setAvailableSeating($availableSeating)
    {
        $this->availableSeating = $availableSeating;

        return $this;
    }

    /**
     * @return boolean
     */
    public function hasAvailableSeating()
    {
        return $this->availableSeating;
    }
}
