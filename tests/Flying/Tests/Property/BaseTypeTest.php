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
     * Serialization tests
     * Each entry is: array($value, $expectedResult, $config)
     * @var array
     */
    protected $_serializationTests = array();

    /**
     * @dataProvider getValueTests
     */
    public function testValues($value, $expected, $config = null)
    {
        if (is_array($config)) {
            // Run test with given config
            $property = $this->getTestProperty($value, $config);
            $this->assertEquals($expected, $property->getValue());

        } else {
            // Test setting value through constructor
            $property = $this->getTestProperty($value, array(
                'nullable' => true,
                'default'  => null,
            ));
            $this->assertEquals($expected, $property->getValue());

            // Test setting value as default with null value allowed
            $property = $this->getTestProperty(null, array(
                'nullable' => true,
                'default'  => $value,
            ));
            $this->assertEquals($expected, $property->getValue());

            // Test setting value as default with null value not allowed
            $property = $this->getTestProperty(null, array(
                'nullable' => false,
                'default'  => $value,
            ));
            $this->assertEquals($expected, $property->getValue());

            // Test setting value explicitly
            $property = $this->getTestProperty(null, array(
                'nullable' => true,
                'default'  => null,
            ));
            $property->setValue($value);
            $this->assertEquals($expected, $property->getValue());
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
        $this->assertNull($property->getValue());
        $property->setValue(null);
        $this->assertNull($property->getValue());
    }

    public function testUnacceptableNullValue()
    {
        $property = $this->getTestProperty(null, array(
            'nullable' => false,
        ));
        $this->assertEquals($property->getValue(), $this->_defaultValue);
    }

    public function serializationDataProvider()
    {
        $tests = array();
        foreach ($this->_serializationTests as $test) {
            $tests[] = array($test, $test, $this->_defaultConfig);
        }
        return $tests;
    }

}
