<?php

namespace NObjects\Tests\Reflection\Fake;

/**
 * PotpourriChild is a fake object used for testing.
 */
class PotpourriChild extends Potpourri
{
    /**
     * Public property
     *
     * @var string
     */
    public $childPubProp;

    /**
     * Protected property
     *
     * @var string
     */
    protected $childProtProp;

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

    /**
     * @param string $childProtProp
     *
     * @return $this
     */
    public function setChildProtProp($childProtProp)
    {
        $this->childProtProp = $childProtProp;

        return $this;
    }

    /**
     * @return string
     */
    public function getChildProtProp()
    {
        return $this->childProtProp;
    }
}
