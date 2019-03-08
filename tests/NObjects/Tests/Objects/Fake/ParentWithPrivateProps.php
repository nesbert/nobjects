<?php

namespace NObjects\Tests\Objects\Fake;

use NObjects\Nobject;

/**
 * ParentWithPrivateProps is a fake testing class.
 */
class ParentWithPrivateProps extends Nobject
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
