<?php

namespace Flying\Tests\Property;

use Flying\Tests\Property\Fixtures\PropertyForUnacceptableValues;
use Flying\Tests\TestCase;

class UnacceptableValueTest extends TestCase
{
    public function testNullValueToNullableProperty()
    {
        $property = new PropertyForUnacceptableValues(null, array(
            'nullable' => true,
        ));
        $this->assertNull($property->getValue());
    }

    public function testNotNullValueToNullableProperty()
    {
        $property = new PropertyForUnacceptableValues('test', array(
            'nullable' => true,
        ));
        $this->assertNull($property->getValue());
    }

    public function testUnacceptableValuePassedToConstructor()
    {
        $property = new PropertyForUnacceptableValues('test', array(
            'nullable' => false,
            'default'  => 'value',
        ));
        $this->assertEquals('value', $property->getValue());
    }
}
