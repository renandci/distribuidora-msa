<?php
namespace Simple\Hydrate\Tests\Unit\Phone;

use PHPUnit\Framework\TestCase;

/**
 * @author Isaque de Souza <isaquesb@gmail.com>
 */
class HydrateTest extends TestCase
{
    public function testStd()
    {
        $ob1 = new \TestObject();
        $this->assertEquals([
            'name' => null,
            'age' => 18,
        ], $ob1->toArray());
    }

    public function testConstruct()
    {
        $obj = new \TestObject('Isaque', 25);
        $this->assertEquals([
            'name' => 'Isaque',
            'age' => 25,
        ], $obj->toArray());
    }

    public function testArray()
    {
        $obj = new \TestObject(['name' => 'John', 'age' => 10]);
        $this->assertEquals([
            'name' => 'John',
            'age' => 10,
        ], $obj->toArray());
    }

    public function testGetNameRuleWithOffisetGet()
    {
        $obj = new \TestObject('ISAQUE');
        $this->assertEquals('Isaque', $obj->name);
    }

    public function testGetAgeWithOffisetSet()
    {
        $obj = new \TestObject('ISAQUE');
        $obj->age = 16;
        $this->assertEquals($obj->getAge(), 16);
    }

    public function testGetNameWithArrayAccess()
    {
        $obj = new \TestObject('ISAQUE');
        $this->assertEquals('Isaque', $obj['name']);
    }
}
