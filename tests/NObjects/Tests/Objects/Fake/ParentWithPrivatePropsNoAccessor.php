<?php

namespace NObjects\Tests\Objects\Fake;

use NObjects\Nobject;

/**
 * ParentWithPrivatePropsNoAccessor is a fake testing class.
 */
class ParentWithPrivatePropsNoAccessor extends Nobject
{
    /**
     * @var string
     */
    private $privParentProp = 'privValue';
}
