<?php

namespace NObjects\Tests\Reflection\Fake;

/**
 * PotpourriChildChild is a fake testing class.
 */
class PotpourriChildChild extends PotpourriChild
{
    /**
     * Private property
     *
     * @var string
     */
    private $childPrivProp;

    /**
     * @param string $childPrivProp
     *
     * @return $this
     */
    public function setChildPrivProp($childPrivProp)
    {
        $this->childPrivProp = $childPrivProp;

        return $this;
    }

    /**
     * @return string
     */
    public function getChildPrivProp()
    {
        return $this->childPrivProp;
    }
}
