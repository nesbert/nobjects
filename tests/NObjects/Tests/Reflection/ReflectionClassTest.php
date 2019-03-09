<?php

namespace NObjects\Tests\Reflection;

use NObjects\Reflection\ReflectionClass;

/**
 * ReflectionClassTest provides unit tests for Reflection\ReflectionClass
 */
class ReflectionClassTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \NObjects\Reflection\ReflectionClass
     */
    protected $object;

    /**
     * tearDown runs at the end of each unit test
     */
    protected function tearDown()
    {
        $this->object = null;

        parent::tearDown();
    }

    /**
     * @covers NObjects\Reflection\ReflectionClass
     */
    public function testGetParentClassReturnsOurReflectionClassInstance()
    {
        $fixture = new Fake\PotpourriChild();

        $this->object = new ReflectionClass($fixture);

        $this->assertInstanceOf('NObjects\Reflection\ReflectionClass', $this->object->getParentClass());
    }

    /**
     * @covers NObjects\Reflection\ReflectionClass
     */
    public function testGetParentClassReturnsFalseWhenNoParent()
    {
        $fixture = new \stdClass();

        $this->object = new ReflectionClass($fixture);
        $this->assertFalse($this->object->getParentClass());
    }

    /**
     * @covers NObjects\Reflection\ReflectionClass
     */
    public function testGetPropertiesReturnsAllPropertiesNoFilter()
    {
        $fixture = new Fake\PotpourriChild();

        $this->object = new ReflectionClass($fixture);
        $results = $this->object->getProperties();

        $this->assertCount(5, $results);

        $this->assertCount(1, array_filter($results, function ($elem) {
            /** @var \ReflectionProperty $elem */
            return $elem->getName() == 'childPubProp';
        }));

        $this->assertCount(1, array_filter($results, function ($elem) {
            /** @var \ReflectionProperty $elem */
            return $elem->getName() == 'childProtProp';
        }));

        $this->assertCount(1, array_filter($results, function ($elem) {
            /** @var \ReflectionProperty $elem */
            return $elem->getName() == 'childPrivProp';
        }));

        $this->assertCount(1, array_filter($results, function ($elem) {
            /** @var \ReflectionProperty $elem */
            return $elem->getName() == 'protProp';
        }));

        $this->assertCount(1, array_filter($results, function ($elem) {
            /** @var \ReflectionProperty $elem */
            return $elem->getName() == 'pubProp';
        }));
    }

    /**
     * @covers NObjects\Reflection\ReflectionClass
     */
    public function testGetPropertiesReturnsPublicProperties()
    {
        $fixture = new Fake\PotpourriChild();

        $this->object = new ReflectionClass($fixture);
        $results = $this->object->getProperties(\ReflectionProperty::IS_PUBLIC);

        $this->assertCount(2, $results);

        $this->assertCount(1, array_filter($results, function ($elem) {
            /** @var \ReflectionProperty $elem */
            return $elem->getName() == 'childPubProp';
        }));

        $this->assertCount(1, array_filter($results, function ($elem) {
            /** @var \ReflectionProperty $elem */
            return $elem->getName() == 'pubProp';
        }));
    }

    /**
     * @covers NObjects\Reflection\ReflectionClass
     */
    public function testGetPropertiesReturnsProtectedProperties()
    {
        $fixture = new Fake\PotpourriChild();

        $this->object = new ReflectionClass($fixture);
        $results = $this->object->getProperties(\ReflectionProperty::IS_PROTECTED);

        $this->assertCount(2, $results);

        $this->assertCount(1, array_filter($results, function ($elem) {
            /** @var \ReflectionProperty $elem */
            return $elem->getName() == 'childProtProp';
        }));

        $this->assertCount(1, array_filter($results, function ($elem) {
            /** @var \ReflectionProperty $elem */
            return $elem->getName() == 'protProp';
        }));
    }

    /**
     * @covers NObjects\Reflection\ReflectionClass
     */
    public function testGetPropertiesReturnsPrivateProperties()
    {
        $fixture = new Fake\PotpourriChild();

        $this->object = new ReflectionClass($fixture);
        $results = $this->object->getProperties(\ReflectionProperty::IS_PRIVATE);

        $this->assertCount(1, $results);

        $this->assertCount(1, array_filter($results, function ($elem) {
            /** @var \ReflectionProperty $elem */
            return $elem->getName() == 'childPrivProp';
        }));
    }

    /**
     * @covers NObjects\Reflection\ReflectionClass
     */
    public function testGetPropertiesReturnsPrivatePropertiesNotChildClass()
    {
        $fixture = new Fake\Potpourri();

        $this->object = new ReflectionClass($fixture);
        $results = $this->object->getProperties(\ReflectionProperty::IS_PRIVATE);

        $this->assertCount(1, $results);

        $this->assertCount(1, array_filter($results, function ($elem) {
            /** @var \ReflectionProperty $elem */
            return $elem->getName() == 'privProp';
        }));
    }

    /**
     * @covers NObjects\Reflection\ReflectionClass
     */
    public function testGetPropertiesReturnsAllPropertiesNoFilterSameResultFromCache()
    {
        $fixture = new Fake\PotpourriChild();

        $this->object = new ReflectionClass($fixture);
        $results = $this->object->getProperties();

        $this->assertCount(5, $results);

        $this->assertCount(1, array_filter($results, function ($elem) {
            /** @var \ReflectionProperty $elem */
            return $elem->getName() == 'childPubProp';
        }));

        $this->assertCount(1, array_filter($results, function ($elem) {
            /** @var \ReflectionProperty $elem */
            return $elem->getName() == 'childProtProp';
        }));

        $this->assertCount(1, array_filter($results, function ($elem) {
            /** @var \ReflectionProperty $elem */
            return $elem->getName() == 'childPrivProp';
        }));

        $this->assertCount(1, array_filter($results, function ($elem) {
            /** @var \ReflectionProperty $elem */
            return $elem->getName() == 'protProp';
        }));

        $this->assertCount(1, array_filter($results, function ($elem) {
            /** @var \ReflectionProperty $elem */
            return $elem->getName() == 'pubProp';
        }));

        // Subsequent calls should be cached
        $results = $this->object->getProperties();

        $this->assertCount(5, $results);

        $this->assertCount(1, array_filter($results, function ($elem) {
            /** @var \ReflectionProperty $elem */
            return $elem->getName() == 'childPubProp';
        }));

        $this->assertCount(1, array_filter($results, function ($elem) {
            /** @var \ReflectionProperty $elem */
            return $elem->getName() == 'childProtProp';
        }));

        $this->assertCount(1, array_filter($results, function ($elem) {
            /** @var \ReflectionProperty $elem */
            return $elem->getName() == 'childPrivProp';
        }));

        $this->assertCount(1, array_filter($results, function ($elem) {
            /** @var \ReflectionProperty $elem */
            return $elem->getName() == 'protProp';
        }));

        $this->assertCount(1, array_filter($results, function ($elem) {
            /** @var \ReflectionProperty $elem */
            return $elem->getName() == 'pubProp';
        }));
    }


    /**
     * @covers NObjects\Reflection\ReflectionClass
     */
    public function testGetPropertiesReturnsPrivatePropertiesSameResultFromCache()
    {
        $fixture = new Fake\Potpourri();

        $this->object = new ReflectionClass($fixture);
        $results = $this->object->getProperties(\ReflectionProperty::IS_PRIVATE);

        $this->assertCount(1, $results);

        $this->assertCount(1, array_filter($results, function ($elem) {
            /** @var \ReflectionProperty $elem */
            return $elem->getName() == 'privProp';
        }));

        // Subsequent calls should be cached
        $results = $this->object->getProperties(\ReflectionProperty::IS_PRIVATE);
        $this->assertCount(1, $results);

        $this->assertCount(1, array_filter($results, function ($elem) {
            /** @var \ReflectionProperty $elem */
            return $elem->getName() == 'privProp';
        }));
    }

    /**
     * @covers \NObjects\Reflection\ReflectionClass
     */
    public function testGetAncestorPrivateProperties()
    {
        $fixture = new Fake\PotpourriChild();

        $this->object = new ReflectionClass($fixture);

        $results = $this->object->getAncestorPrivateProperties();

        $this->assertCount(1, $results);
        $this->assertCount(1, array_filter($results, function ($elem) {
            /** @var \ReflectionProperty $elem */
            return $elem->getName() == 'privProp';
        }), 'Ancestor private property not in collection');
    }

    /**
     * @covers \NObjects\Reflection\ReflectionClass
     */
    public function testGetAncestorPrivatePropertiesReturnsPropertyNameOnlyOnce()
    {
        $fixture = new Fake\PotpourriChildChildChild();

        $this->object = new ReflectionClass($fixture);

        $results = $this->object->getAncestorPrivateProperties();

        $this->assertCount(2, $results);
        $this->assertCount(1, array_filter($results, function ($elem) {
            /** @var \ReflectionProperty $elem */
            return $elem->getName() == 'privProp';
        }), 'Ancestor private property privProp not in collection');
        $this->assertCount(1, array_filter($results, function ($elem) {
            /** @var \ReflectionProperty $elem */
            return $elem->getName() == 'childPrivProp';
        }), 'Ancestor private property childPrivProp not in collection');
    }
}
