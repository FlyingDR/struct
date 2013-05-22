<?php

namespace Flying\Tests\Property;

abstract class BaseTypeTest extends BasePropertyTest
{

    public function testAcceptableNullValue()
    {
        $property = $this->getTestProperty(null, array(
            'nullable' => true,
        ));
        $this->assertNull($property->get());
        $property->set(null);
        $this->assertNull($property->get());
    }

    public function testUnacceptableNullValue()
    {
        $property = $this->getTestProperty(null, array(
            'nullable' => false,
        ));
        $this->assertEquals($property->get(), $this->_defaultValue);
    }

}
