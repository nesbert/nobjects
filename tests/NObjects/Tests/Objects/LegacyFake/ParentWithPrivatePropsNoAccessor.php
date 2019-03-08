<?php

namespace NObjects\Tests\Objects\LegacyFake;

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
