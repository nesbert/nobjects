<?php

namespace NObjects\Tests\Reflection\Fake;

use NObjects\Nobject;

/**
 * Potpourri is a fake object used for testing.
 */
class Potpourri extends Nobject
{
    /**
     * Public object property
     * @var string
     */
    public $pubProp;

    /**
     * Protected object property
     *
     * @var string
     */
    protected $protProp;

    /**
     * Private object property
     *
     * @var string
     */
    private $privProp;

    /**
     * @param string $privProp
     *
     * @return $this
     */
    public function setPrivProp($privProp)
    {
        $this->privProp = $privProp;

        return $this;
    }

    /**
     * @return string
     */
    public function getPrivProp()
    {
        return $this->privProp;
    }

    /**
     * @param string $protProp
     *
     * @return $this
     */
    public function setProtProp($protProp)
    {
        $this->protProp = $protProp;

        return $this;
    }

    /**
     * @return string
     */
    public function getProtProp()
    {
        return $this->protProp;
    }
}
