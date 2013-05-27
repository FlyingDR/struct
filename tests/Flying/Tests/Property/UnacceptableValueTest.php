<?php

namespace Flying\Tests\Property;

use Flying\Tests\Property\Fixtures\PropertyForUnacceptableValues;

class UnacceptableValueTest extends \PHPUnit_Framework_TestCase
{

    public function testNullValueToNullableProperty()
    {
        $property = new PropertyForUnacceptableValues(null, array(
            'nullable' => true,
        ));
        $this->assertNull($property->get());
    }

    public function testNotNullValueToNullableProperty()
    {
        $property = new PropertyForUnacceptableValues('test', array(
            'nullable' => true,
        ));
        $this->assertNull($property->get());
    }

    public function testUnacceptableValuePassedToConstructor()
    {
        $property = new PropertyForUnacceptableValues('test', array(
            'nullable' => false,
            'default'  => 'value',
        ));
        $this->assertEquals('value', $property->get());
    }

}