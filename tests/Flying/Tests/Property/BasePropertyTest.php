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
     * @dataProvider serializationDataProvider
     */
    public function testSerialization($value, $expected, $config = null)
    {
        $property = $this->getTestProperty($value, $config);
        $testSerialize = function ($value) use ($property) {
            $value = serialize($value);
            $class = get_class($property);
            $result = sprintf('C:%d:"%s":%d:{%s}', strlen($class), $class, strlen($value), $value);
            return $result;
        };
        $serialized = serialize($property);
        $this->assertEquals($testSerialize($value), $serialized);
        $p = unserialize($serialized);
        $this->assertEquals($value, $p->get());
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
     * @return \Flying\Struct\Property\Property
     */
    protected function getTestProperty($value = null, $config = null)
    {
        $class = $this->getPropertyClass();
        if (!is_subclass_of($class, 'Flying\Struct\Property\Property')) {
            $this->fail('Test property class must be inherited from Property');
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

    abstract public function serializationDataProvider();

}