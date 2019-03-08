<?php

namespace NObjects\Tests\Objects\LegacyFake;

use NObjects\Object;

/**
 * ParentWithPrivateProps is a fake testing class.
 */
class ParentWithPrivateProps extends Object
{
    /**
     * @var string
     */
    private $privParentProp = 'privValue';

    /**
     * @param string $privParentProp
     *
     * @return $this
     */
    public function setPrivParentProp($privParentProp)
    {
        $this->privParentProp = $privParentProp;

        return $this;
    }

    /**
     * @return string
     */
    public function getPrivParentProp()
    {
        return $this->privParentProp;
    }
}
