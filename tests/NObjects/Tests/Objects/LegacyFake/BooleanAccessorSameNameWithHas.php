<?php

namespace NObjects\Tests\Objects\LegacyFake;

use NObjects\Object;

/**
 * BooleanAccessorSameNameWithHas is a fake testing object.
 */
class BooleanAccessorSameNameWithHas extends Object
{
    /**
     * @var bool
     */
    protected $hasAvailableSeating;

    /**
     * @param boolean $hasAvailableSeating
     *
     * @return $this
     */
    public function setHasAvailableSeating($hasAvailableSeating)
    {
        $this->hasAvailableSeating = $hasAvailableSeating;

        return $this;
    }

    /**
     * @return boolean
     */
    public function hasAvailableSeating()
    {
        return $this->hasAvailableSeating;
    }
}
