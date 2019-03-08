<?php

namespace NObjects\Tests\Objects\Fake;

use NObjects\Nobject;

/**
 * BooleanAccessorDifferentNameWithHas is a fake testing object.
 */
class BooleanAccessorDifferentNameWithHas extends Nobject
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
