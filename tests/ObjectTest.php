<?php
namespace NObjects\Tests;

use NObjects\Object;

class ObjectTest extends \PHPUnit_Framework_TestCase
{
    public function testObjectHelpers()
    {
        // simple
        $data = array('test' => 1212);
        $obj = new \NObjects\Object();
        $this->assertTrue($obj instanceof Object);
        $this->assertEquals($obj, $obj->fromArray($data));
        $this->assertTrue(isset($obj->test));
        $this->assertEquals($data['test'], $obj->test);
        $this->assertEquals($data, $obj->toArray());
        $this->assertEquals(json_encode($data), $obj->toJSON());
        $this->assertEquals(array_keys($data), $obj->getProperties());
        $this->assertEquals(array_values($data), $obj->getPropertyValues());

        // simple 2 load via construct
        $obj2 = new \NObjects\Object($data);
        $this->assertTrue($obj2 instanceof Object);
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
            'favorites' => new \NObjects\Object( (object)array('toy'=>'Legos') ),
        );

        $data3['nested'] = $data3;

        $obj3 = new \NObjects\Object($data3);

        $this->assertTrue($obj3 instanceof Object);

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

        $foo = new FooOne();
        $this->assertEquals($foo, $foo->fromArray(array('bars' => new \ArrayObject(array(1,2,3)))));
        $this->assertEquals(new \ArrayObject(array(1,2,3)), $foo->getBars());
        $this->assertEquals($foo->toJSON(), (string)$foo);
    }

    public function testAncestors()
    {
        $ancestors = Object::ancestors('Object');
        $this->assertTrue(empty($ancestors));

        $ancestors = Object::ancestors('\NObjects\DateTime');
        $this->assertFalse(empty($ancestors));
        $this->assertTrue(is_array($ancestors));
        $this->assertTrue(count($ancestors)==1);
        $this->assertEquals('DateTime', $ancestors[0]);
    }

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
            } else if ($value instanceof \DateTime) {
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
}

class FooOne extends Object
{
    private $bars;

    public function setBars(\ArrayObject $bar)
    {
        $this->bars = $bar;
    }

    public function getBars()
    {
        return $this->bars;
    }
}
