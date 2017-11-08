<?php

namespace Flying\Tests\Property;

abstract class BaseTypeTest extends BasePropertyTest
{
    /**
     * Serialization tests
     * Each entry is: array($value, $expectedResult, $config)
     *
     * @var array
     */
    protected $serializationTests = array();

    /**
     * @dataProvider getValueTests
     */
    public function testValues($value, $expected, $config = null)
    {
        if (is_array($config)) {
            // Run test with given config
            $property = $this->getTestProperty($value, $config);
            static::assertEquals($expected, $property->getValue());

        } else {
            // Test setting value through constructor
            $property = $this->getTestProperty($value, array(
                'nullable' => true,
                'default'  => null,
            ));
            static::assertEquals($expected, $property->getValue());

            // Test setting value as default with null value allowed
            $property = $this->getTestProperty(null, array(
                'nullable' => true,
                'default'  => $value,
            ));
            static::assertEquals($expected, $property->getValue());

            // Test setting value as default with null value not allowed
            $property = $this->getTestProperty(null, array(
                'nullable' => false,
                'default'  => $value,
            ));
            static::assertEquals($expected, $property->getValue());

            // Test setting value explicitly
            $property = $this->getTestProperty(null, array(
                'nullable' => true,
                'default'  => null,
            ));
            $property->setValue($value);
            static::assertEquals($expected, $property->getValue());
        }
    }

    public function testAcceptableNullValue()
    {
        $property = $this->getTestProperty(null, array(
            'nullable' => true,
            'default'  => null,
        ));
        static::assertNull($property->getValue());
        $property->setValue(null);
        static::assertNull($property->getValue());
    }

    public function testUnacceptableNullValue()
    {
        $defaultValue = $this->getDefaultValue();
        $property = $this->getTestProperty(null, array(
            'nullable' => false,
        ));
        static::assertEquals($property->getValue(), $defaultValue);
    }

    public function serializationDataProvider()
    {
        $tests = array();
        foreach ($this->serializationTests as $test) {
            $tests[] = array($test, $test, $this->getDefaultConfig());
        }
        return $tests;
    }

    /**
     * Get value validation tests
     * Each entry is: array($value, $expectedResult, $config)
     *
     * @return array
     */
    abstract public function getValueTests();
}
