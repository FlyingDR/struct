<?php

namespace Flying\Tests\Property;

abstract class BasePropertyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Class name of the property to test
     * @var string
     */
    protected $_propertyClass = null;
    /**
     * Namespace of property class to test
     * @var string
     */
    protected $_propertyNs = 'Flying\Struct\Property';
    /**
     * Default configuration options for test
     * @var array
     */
    protected $_defaultConfig = array(
        'nullable' => false,
        'default'  => null,
    );
    /**
     * Default property value
     * @var string
     */
    protected $_defaultValue = 'default value';
    /**
     * Value validation tests
     * Each entry is: array($value, $expectedResult, $config)
     * @var array
     */
    protected $_valueTests = array();

    public function testValues()
    {
        $tests = $this->getValueTests();
        foreach ($tests as $test) {
            $value = array_shift($test);
            $expected = array_shift($test);
            $config = array_shift($test);

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

                // Test setting value as default
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
    }

    protected function getValueTests()
    {
        return $this->_valueTests;
    }

    /**
     * Get class name of the property to test
     *
     * @return string
     */
    protected function getPropertyClass()
    {
        $class = $this->_propertyClass;
        if (!class_exists($class, true)) {
            $class = trim($this->_propertyNs, '\\') . '\\' . trim($class, '\\');
            if (!class_exists($class, true)) {
                $this->fail('Unable to find test property class: ' . $this->_propertyClass);
            }
        }
        return $class;
    }

    /**
     * Get property object to be tested
     *
     * @param mixed $value
     * @param array $config
     * @return \Flying\Struct\Property\AbstractProperty
     */
    protected function getTestProperty($value = null, $config = null)
    {
        $class = $this->getPropertyClass();
        if (!is_subclass_of($class, 'Flying\Struct\Property\AbstractProperty')) {
            $this->fail('Test property class must be inherited from AbstractProperty');
        }
        if (!is_array($config)) {
            $config = array();
        }
        $defaults = $this->_defaultConfig;
        $defaults['default'] = $this->_defaultValue;
        $config = array_merge($defaults, $config);
        $property = new $class($value, $config);
        return $property;
    }

}
