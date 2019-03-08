<?php

namespace NObjects\Tests\Objects\Fake;

use NObjects\Nobject;

/**
 * BooleanAccessorSameNameWithHas is a fake testing object.
 */
class BooleanAccessorSameNameWithHas extends Nobject
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
