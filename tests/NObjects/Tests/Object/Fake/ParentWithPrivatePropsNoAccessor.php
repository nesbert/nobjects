<?php

namespace NObjects\Tests\Object\Fake;

use NObjects\Object;

/**
 * ParentWithPrivatePropsNoAccessor is a fake testing class.
 */
class ParentWithPrivatePropsNoAccessor extends Object
{
    /**
     * @var string
     */
    private $privParentProp = 'privValue';
}
