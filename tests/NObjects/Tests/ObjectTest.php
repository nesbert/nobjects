<?php

namespace NObjects\Tests;

use NObjects\Tests\Objects\LegacyFake;
use NObjects\Object;

class ObjectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @group legacy
     *
     * @expectedDeprecation Class NObjects\Object is deprecated. Migrate your code to NObjects\Nobject.
     * @expectedDeprecation Class NObjects\Object is deprecated. Migrate your code to NObjects\Nobject.
     * @expectedDeprecation Class NObjects\Object is deprecated. Migrate your code to NObjects\Nobject.
     * @expectedDeprecation Class NObjects\Object is deprecated. Migrate your code to NObjects\Nobject.
     * @expectedDeprecation Class NObjects\Object is deprecated. Migrate your code to NObjects\Nobject.
     */
    public function testObjectHelpers()
    {
        // simple
        $data = array('test' => 1212);
        $obj = new \NObjects\Object();
        $this->assertTrue($obj instanceof \NObjects\Nobject);
        $this->assertEquals($obj, $obj->fromArray($data));
        $this->assertTrue(isset($obj->test));
        $this->assertEquals($data['test'], $obj->test);
        $this->assertEquals($data, $obj->toArray());
        $this->assertEquals(json_encode($data), $obj->toJSON());
        $this->assertEquals(array_keys($data), $obj->getProperties());
        $this->assertEquals(array_values($data), $obj->getPropertyValues());

        // simple 2 load via construct
        $obj2 = new \NObjects\Object($data);
        $this->assertTrue($obj2 instanceof \NObjects\Nobject);
        $this->assertTrue(isset($obj2->test));
        $this->assertEquals($data['test'], $obj2->test);
        $this->assertEquals($data, $obj2->toArray());
        $this->assertEquals(json_encode($data), $obj2->toJSON());
        $this->assertEquals(array_keys($data), $obj2->getProperties());
        $this->assertEquals(array_values($data), $obj2->getPropertyValues());

        // complex
        $data3 = array(
            'id' => 555,
            'foo' => 'bar',
            'roles' => array('Father','Husband','Geek','Developer','Thinker'),
            'movie' => (object) array('name' => 'Star Wars'),
            'favorites' => new \NObjects\Object((object)array('toy'=>'Legos')),
        );

        $data3['nested'] = $data3;

        $obj3 = new \NObjects\Object($data3);

        $this->assertTrue($obj3 instanceof \NObjects\Nobject);

        foreach ($data3 as $k => $v) {
            $this->assertTrue(isset($obj3->{$k}));
            $this->assertEquals($data3[$k], $obj3->{$k});
        }

        $this->assertTrue(is_array($obj3->toArray()));
        $this->assertEquals(json_encode($data3), $obj3->toJSON());
        $this->assertNotEquals(json_encode($data), $obj3->toJSON());
        $this->assertEquals(array_keys($data3), $obj3->getProperties());

        // NObject get translated to an array
        $this->assertNotEquals(array_values($data3), $obj3->getPropertyValues());

        // Gracefully handle empty keys in an associative array
        $data4 = array('test' => 1212, '' => 'bar');
        $obj4  = new \NObjects\Object($data4);
        $this->assertTrue($obj4 instanceof \NObjects\Nobject);
        $this->assertEquals(array('test' => 1212), $obj4->toArray()); // value for empty key ignored
    }

    /**
     * @group legacy
     * @expectedDeprecation Class NObjects\Object is deprecated. Migrate your code to NObjects\Nobject.
     */
    public function testObjectHelpersWithPublicPropsAndSetters()
    {
        $data = array('bar' => '1212');
        $obj = new LegacyFake\FooFour();
        $obj->fromArray($data);

        $this->assertEquals('1212', $obj->getBar());
        $this->assertEquals('1212', $obj->bar);

        $outData = $obj->toArray();
        $this->assertEquals(array('bar' => '1212'), $outData);
    }

    /**
     * @group legacy
     * @expectedDeprecation Class NObjects\Object is deprecated. Migrate your code to NObjects\Nobject.
     */
    public function testAncestors()
    {
        $ancestors = Object::ancestors('Nobject');
        $this->assertTrue(empty($ancestors));

        $ancestors = Object::ancestors('\NObjects\DateTime');
        $this->assertFalse(empty($ancestors));
        $this->assertTrue(is_array($ancestors));
        $this->assertTrue(count($ancestors)==1);
        $this->assertEquals('DateTime', $ancestors[0]);
    }

    /**
     * @group legacy
     * @expectedDeprecation Class NObjects\Object is deprecated. Migrate your code to NObjects\Nobject.
     * @expectedDeprecation Class NObjects\Object is deprecated. Migrate your code to NObjects\Nobject.
     */
    public function testValueClosure()
    {
        $date = '2012-05-02T12:00:00Z';
        $date2 = '2012-08-09T12:00:00+00:00';
        $data = array(
            'name' => 'testing',
            'date' => new \NObjects\DateTime($date),
            'date2' => new \DateTime($date2),
        );
        $obj = new \NObjects\Object($data);

        // test value
        $this->assertEquals(new \NObjects\DateTime($date), $obj->date);

        $closure = function ($value) {
            // flatten date objects
            if ($value instanceof \NObjects\DateTime) {
                $value = $value->toISO8601();
            } elseif ($value instanceof \DateTime) {
                $value = $value->format('c');
            }

            return $value;
        };

        $closureData = $obj->toArray($closure);

        $this->assertEquals($date, $closureData['date']);
        $this->assertEquals($data['date2']->format('c'), $closureData['date2']);

        // test nested arrays
        $data = array(
            'fooBars' => new \ArrayObject(array($data, $data, $data))
        );

        $obj = new \NObjects\Object($data);

        // test closures
        $closureData = $obj->toArray($closure);

        foreach ($closureData['fooBars'] as $bar) {
            $this->assertEquals($date, $bar['date']);
            $this->assertEquals($date2, $bar['date2']);
        }
    }

    /**
     * @group legacy
     * @expectedDeprecation Class NObjects\Object is deprecated. Migrate your code to NObjects\Nobject.
     */
    public function testToArrayMore()
    {
        $bars = array(1,2,3,4,5);
        $data = array('test' => 'testToArrayMore', 'fooTwo' => new LegacyFake\FooTwo($bars));
        $obj = new \NObjects\Object($data);

        $data['fooTwo'] = $bars;
        $this->assertEquals($data, $obj->toArray());
    }

    /**
     * @group legacy
     * @expectedDeprecation Class NObjects\Object is deprecated. Migrate your code to NObjects\Nobject.
     * @expectedDeprecation Class NObjects\Object is deprecated. Migrate your code to NObjects\Nobject.
     * @expectedDeprecation Class NObjects\Object is deprecated. Migrate your code to NObjects\Nobject.
     * @expectedDeprecation Class NObjects\Object is deprecated. Migrate your code to NObjects\Nobject.
     */
    public function testTypeHintingHelpers()
    {
        $data = array(
            'int1' => '123',
            'string1' => 123,
            'bool1' => 1,
            'bool0' => 0,
            'float' => 555.55559,
            'double' => 555.55559,
            'array' => (object)array('foo'=>'bar'),
            'instanceObject' => array('bar'=>'blah'),
            'instanceArrayObject' => array(1,2,3,4,5),
        );

        $obj = new \NObjects\Object();

        // int
        $this->assertEquals((int)$data['int1'], $obj->_initInt($data['int1']));
        $this->assertTrue((int)$data['int1'] === $obj->_initInt($data['int1']));
        $this->assertNull($obj->_initInt(null));

        // string
        $this->assertEquals((string)$data['string1'], $obj->_initStr($data['string1']));
        $this->assertTrue((string)$data['string1'] === $obj->_initStr($data['string1']));
        $this->assertNull($obj->_initStr(null));

        // boolean
        $this->assertEquals((bool)$data['bool1'], $obj->_initBool($data['bool1']));
        $this->assertTrue((bool)$data['bool1'] === $obj->_initBool($data['bool1']));
        $this->assertNull($obj->_initBool(null));

        // float
        $this->assertEquals((float)$data['float'], $obj->_initFloat($data['float']));
        $this->assertTrue((float)$data['float'] === $obj->_initFloat($data['float']));
        $this->assertNull($obj->_initFloat(null));

        // double
        $this->assertEquals((double)$data['double'], $obj->_initDouble($data['double']));
        $this->assertTrue((double)$data['double'] === $obj->_initDouble($data['double']));
        $this->assertNull($obj->_initDouble(null));

        // array
        $this->assertEquals((array)$data['array'], $obj->_initArray($data['array']));
        $this->assertTrue((array)$data['array'] === $obj->_initArray($data['array']));
        $this->assertNull($obj->_initArray(null));

        // Nobject instance
        $valType = 'NObjects\Tests\Objects\LegacyFake\FooThree';
        $this->assertEquals(
            new LegacyFake\FooThree($data['instanceObject']),
            $obj->_initInstanceObject($data['instanceObject'], $valType)
        );
        $this->assertInstanceOf($valType, $obj->_initInstanceObject($data['instanceObject'], $valType));
        $this->assertNull($obj->_initInstanceObject(null, $valType));

        // ArrayObject instance
        $valType = '\ArrayObject';
        $ints = $obj->_initInstanceArrayObject($data['instanceArrayObject'], 'integer');
        $strs = $obj->_initInstanceArrayObject($data['instanceArrayObject'], 'string');
        $this->assertEquals(new \ArrayObject($data['instanceArrayObject']), $ints);
        $this->assertEquals(new \ArrayObject($data['instanceArrayObject']), $strs);
        $this->assertInstanceOf($valType, $ints);
        $this->assertInstanceOf($valType, $strs);
        $this->assertNull($obj->_initInstanceArrayObject(null, $valType));

        foreach ($ints as $int) {
            $this->assertTrue(is_integer($int));
        }

        foreach ($strs as $str) {
            $this->assertTrue(is_string($str));
        }
    }

    /**
     * @group legacy
     * @expectedDeprecation Class NObjects\Object is deprecated. Migrate your code to NObjects\Nobject.
     */
    public function testToJSONCreatesJSONString()
    {
        $fixture = new LegacyFake\FooOne();
        $this->assertEquals(
            $fixture,
            $fixture->fromArray(
                array(
                    'bars' => new \ArrayObject(array(1, 2, 3)),
                    'skip' => true
                )
            )
        );
        $this->assertJson($fixture->toJSON());
        $this->assertJsonStringEqualsJsonString($fixture->toJSON(), '{ "bars": [1, 2, 3] }');
    }

    /**
     * @group legacy
     * @expectedDeprecation Class NObjects\Object is deprecated. Migrate your code to NObjects\Nobject.
     */
    public function testToStringCreatesJSONString()
    {
        $fixture = new LegacyFake\FooOne();
        $this->assertEquals(
            $fixture,
            $fixture->fromArray(
                array(
                    'bars' => new \ArrayObject(array(1, 2, 3)),
                    'skip' => true
                )
            )
        );
        $this->assertJson((string) $fixture);
        $this->assertJsonStringEqualsJsonString((string) $fixture, '{ "bars": [1, 2, 3] }');
    }

    /**
     * @group legacy
     * @expectedDeprecation Class NObjects\Object is deprecated. Migrate your code to NObjects\Nobject.
     */
    public function testToArrayOmitsPropertiesBeginningWithUnderscore()
    {
        $fixture = new LegacyFake\FooOne();
        $this->assertEquals(
            $fixture,
            $fixture->fromArray(
                array(
                    'bars' => new \ArrayObject(array(1, 2, 3)),
                    'skip' => true
                )
            )
        );
        $this->assertEquals(array('bars' => array(1, 2, 3)), $fixture->toArray());
        $this->assertEquals(new \ArrayObject(array(1, 2, 3)), $fixture->getBars());
    }

    /**
     * @group legacy
     * @expectedDeprecation Class NObjects\Object is deprecated. Migrate your code to NObjects\Nobject.
     */
    public function testToArrayIncludesDynamicPublicProperties()
    {
        $fixture = new LegacyFake\FooOne();
        $fixture->fromArray(array('bars' => new \ArrayObject(array(1, 2, 3)), 'skip' => true));

        $fixture->newProperty = 'dynamicVal';

        $this->assertEquals(array('newProperty' => 'dynamicVal', 'bars' => array(1, 2, 3)), $fixture->toArray());
    }

    /**
     * @group legacy
     * @expectedDeprecation Class NObjects\Object is deprecated. Migrate your code to NObjects\Nobject.
     */
    public function testToArrayIncludesInheritedPrivatePropsWithAccessors()
    {
        $fixture = new LegacyFake\ChildOfParentWithPrivateProps();
        $fixture->foo = 'fooValue';
        $fixture->setPrivParentProp('barValue');

        $this->assertEquals(array('foo' => 'fooValue', 'privParentProp' => 'barValue'), $fixture->toArray());
    }

    /**
     * @group legacy
     * @expectedDeprecation Class NObjects\Object is deprecated. Migrate your code to NObjects\Nobject.
     */
    public function testToArrayOmitsInheritedPrivatePropsWithoutAccessors()
    {
        $fixture = new LegacyFake\ChildOfParentWithPrivatePropsNoAccessor();
        $fixture->foo = 'fooValue';

        $this->assertEquals(array('foo' => 'fooValue'), $fixture->toArray());
    }

    /**
     * @group legacy
     * @expectedDeprecation Class NObjects\Object is deprecated. Migrate your code to NObjects\Nobject.
     */
    public function testToArrayIncludesBooleansWithIsConventionAccessorsWithSameName()
    {
        $fixture = new LegacyFake\BooleanAccessorSameName();
        $fixture->setIsStored(true);

        $this->assertEquals(array('isStored' => true), $fixture->toArray());
    }

    /**
     * @group legacy
     * @expectedDeprecation Class NObjects\Object is deprecated. Migrate your code to NObjects\Nobject.
     */
    public function testToArrayIncludesBooleanWithIsConventionAccessors()
    {
        $fixture = new LegacyFake\BooleanAccessorDifferentName();
        $fixture->setStored(true);

        $this->assertEquals(array('stored' => true), $fixture->toArray());
    }

    /**
     * @group legacy
     * @expectedDeprecation Class NObjects\Object is deprecated. Migrate your code to NObjects\Nobject.
     */
    public function testToArrayIncludesBooleansWithHasConventionAccessorsWithSameName()
    {
        $fixture = new LegacyFake\BooleanAccessorSameNameWithHas();
        $fixture->setHasAvailableSeating(true);

        $this->assertEquals(array('hasAvailableSeating' => true), $fixture->toArray());
    }

    /**
     * @group legacy
     * @expectedDeprecation Class NObjects\Object is deprecated. Migrate your code to NObjects\Nobject.
     */
    public function testToArrayIncludesBooleanWithHasConventionAccessors()
    {
        $fixture = new LegacyFake\BooleanAccessorDifferentNameWithHas();
        $fixture->setAvailableSeating(true);

        $this->assertEquals(array('availableSeating' => true), $fixture->toArray());
    }

    /**
     * @group legacy
     * @expectedDeprecation Class NObjects\Object is deprecated. Migrate your code to NObjects\Nobject.
     */
    public function testFromArrayHydratesSubobjects()
    {
        $fixture = new LegacyFake\ObjectWithChild();

        $fixture->fromArray(
            array(
                'foo' => 'x',
                'childObject' => array(
                    'foo' => 'a',
                    'bar' => 'b',
                    'baz' => 'c'
                )
            )
        );

        $this->assertEquals('x', $fixture->getFoo());
        $this->assertEquals('a', $fixture->getChildObject()->getFoo());
        $this->assertEquals('b', $fixture->getChildObject()->getBar());
        $this->assertEquals('c', $fixture->getChildObject()->getBaz());
    }
}
