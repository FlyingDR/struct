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
    protected $propertyClass;
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
    protected $defaultConfig = [
        'nullable' => false,
        'default'  => null,
    ];
    /**
     * Default property value
     *
     * @var string
     */
    protected $defaultValue = 'default value';

    /**
     * @dataProvider serializationDataProvider
     * @param mixed $value
     * @param mixed $expected
     * @param array|null $config
     */
    public function testSerialization($value, $expected, $config = null)
    {
        $property = $this->getTestProperty($value, $config);
        $serialized = serialize($property);
        /** @var $p Property */
        $p = unserialize($serialized);
        static::assertEquals($expected, $p->getValue());
        static::assertEquals($property->getConfig(), $p->getConfig());
    }

    /**
     * Get property object to be tested
     *
     * @param mixed $value
     * @param array $config
     * @return Property
     */
    protected function getTestProperty($value = null, $config = null)
    {
        $class = $this->getPropertyClass();
        if (!is_subclass_of($class, Property::class)) {
            static::fail('Test property class must be inherited from Property');
        }
        if (!is_array($config)) {
            $config = [];
        }
        $defaults = $this->getDefaultConfig();
        $defaultValue = $this->getDefaultValue();
        $defaults['default'] = $defaultValue;
        $config = array_merge($defaults, $config);
        return new $class($value, $config);
    }

    /**
     * Get class name of the property to test
     *
     * @return string
     */
    protected function getPropertyClass()
    {
        $class = $this->propertyClass;
        if (!class_exists($class)) {
            $class = trim($this->propertyNs, '\\') . '\\' . trim($class, '\\');
            /** @noinspection NotOptimalIfConditionsInspection */
            if (!class_exists($class)) {
                static::fail('Unable to find test property class: ' . $this->propertyClass);
            }
        }
        return $class;
    }

    /**
     * @return array
     */
    protected function getDefaultConfig()
    {
        return $this->defaultConfig;
    }

    /**
     * @return mixed
     */
    protected function getDefaultValue()
    {
        return $this->defaultValue;
    }

    abstract public function serializationDataProvider();
}
