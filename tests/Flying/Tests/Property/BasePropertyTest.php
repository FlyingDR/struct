<?php

namespace Flying\Tests\Property;

use Flying\Struct\Property\Property;
use Flying\Tests\TestCase;

abstract class BasePropertyTest extends TestCase
{
    /**
     * Class name of the property to test
     *
     * @var string
     */
    protected $propertyClass = null;
    /**
     * Namespace of property class to test
     *
     * @var string
     */
    protected $propertyNs = 'Flying\Struct\Property';
    /**
     * Default configuration options for test
     *
     * @var array
     */
    protected $defaultConfig = array(
        'nullable' => false,
        'default'  => null,
    );
    /**
     * Default property value
     *
     * @var string
     */
    protected $defaultValue = 'default value';

    /**
     * @dataProvider serializationDataProvider
     */
    public function testSerialization($value, $expected, $config = null)
    {
        $property = $this->getTestProperty($value, $config);
        $serialized = serialize($property);
        /** @var $p Property */
        $p = unserialize($serialized);
        $this->assertEquals($expected, $p->getValue());
        $this->assertEquals($property->getConfig(), $p->getConfig());
    }

    /**
     * Get class name of the property to test
     *
     * @return string
     */
    protected function getPropertyClass()
    {
        $class = $this->propertyClass;
        if (!class_exists($class, true)) {
            $class = trim($this->propertyNs, '\\') . '\\' . trim($class, '\\');
            if (!class_exists($class, true)) {
                $this->fail('Unable to find test property class: ' . $this->propertyClass);
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
        $defaults = $this->getDefaultConfig();
        $defaults['default'] = $this->getDefaultValue();
        $config = array_merge($defaults, $config);
        $property = new $class($value, $config);
        return $property;
    }

    /**
     * @return mixed
     */
    protected function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * @return array
     */
    protected function getDefaultConfig()
    {
        return $this->defaultConfig;
    }

    abstract public function serializationDataProvider();
}
