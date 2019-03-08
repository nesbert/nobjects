<?php

namespace NObjects\Tests\Objects\Fake;

/**
 * ChildOfParentWithPrivatePropsNoAccessor is a fake testing class.
 */
class ChildOfParentWithPrivatePropsNoAccessor extends ParentWithPrivatePropsNoAccessor
{
    /**
     * @var string
     */
    public $foo;
}
