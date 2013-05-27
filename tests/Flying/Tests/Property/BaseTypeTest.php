<?php

namespace Flying\Tests\Property;

abstract class BaseTypeTest extends BasePropertyTest
{
    /**
     * Value validation tests
     * Each entry is: array($value, $expectedResult, $config)
     * @var array
     */
    protected $_valueTests = array();

    /**
     * @dataProvider getValueTests
     */
    public function testValues($value, $expected, $config = null)
    {
        if (is_array($config)) {
            // Run test with given config
            $property = $this->getTestProperty($value, $config);
            $this->assertEquals($expected, $property->get());

        } else {
            // Test setting value through constructor
            $property = $this->getTestProperty($value, array(
                'nullable' => true,
                'default'  => null,
            ));
            $this->assertEquals($expected, $property->get());

            // Test setting value as default with null value allowed
            $property = $this->getTestProperty(null, array(
                'nullable' => true,
                'default'  => $value,
            ));
            $this->assertEquals($expected, $property->get());

            // Test setting value as default with null value not allowed
            $property = $this->getTestProperty(null, array(
                'nullable' => false,
                'default'  => $value,
            ));
            $this->assertEquals($expected, $property->get());

            // Test setting value explicitly
            $property = $this->getTestProperty(null, array(
                'nullable' => true,
                'default'  => null,
            ));
            $property->set($value);
            $this->assertEquals($expected, $property->get());
        }
    }

    public function getValueTests()
    {
        return $this->_valueTests;
    }

    public function testAcceptableNullValue()
    {
        $property = $this->getTestProperty(null, array(
            'nullable' => true,
            'default'  => null,
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
