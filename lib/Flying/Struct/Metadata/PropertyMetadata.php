<?php

namespace Flying\Struct\Metadata;

/**
 * Structure property metadata storage class
 */
class PropertyMetadata implements MetadataInterface
{
    /**
     * Property name
     * @var string
     */
    protected $_name;
    /**
     * Class name for property object
     * @var string
     */
    protected $_class;
    /**
     * Configuration options for property object
     * @var array
     */
    protected $_config = array();

    /**
     * Class constructor
     *
     * @param string $name      OPTIONAL Property name
     * @param string $class     OPTIONAL Class name for property object
     * @param array $config     OPTIONAL Configuration options for property object
     * @return PropertyMetadata
     */
    public function __construct($name = null, $class = null, $config = null)
    {
        if ($name !== null) {
            $this->setName($name);
        }
        if ($class !== null) {
            $this->setClass($class);
        }
        if ($config !== null) {
            $this->setConfig($config);
        }
    }

    /**
     * Get property name
     *
     * @return mixed
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Set property name
     *
     * @param string $name
     * @throws \InvalidArgumentException
     * @return $this
     */
    public function setName($name)
    {
        if ((!is_string($name)) && ($name !== null)) {
            throw new \InvalidArgumentException('Property name must be a string');
        }
        $this->_name = $name;
        return $this;
    }

    /**
     * Get class name for property object
     *
     * @return string
     */
    public function getClass()
    {
        return $this->_class;
    }

    /**
     * Set class name for property object
     *
     * @param string $class
     * @throws \InvalidArgumentException
     * @return $this
     */
    public function setClass($class)
    {
        if ((!is_string($class)) && ($class !== null)) {
            throw new \InvalidArgumentException('Property class name must be a string');
        }
        $this->_class = $class;
        return $this;
    }

    /**
     * Get configuration options for property object
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->_config;
    }

    /**
     * Set configuration options for property object
     *
     * @param array $config
     * @return $this
     */
    public function setConfig(array $config)
    {
        $this->_config = $config;
        return $this;
    }

    /**
     * Defined by Serializable interface
     *
     * @return string
     */
    public function serialize()
    {
        return (serialize($this->toArray()));
    }

    /**
     * Defined by Serializable interface
     *
     * @param string $serialized
     * @return void
     */
    public function unserialize($serialized)
    {
        $array = @unserialize($serialized);
        if (!is_array($array)) {
            return;
        }
        if (array_key_exists('name', $array)) {
            $this->setName($array['name']);
        }
        if (array_key_exists('class', $array)) {
            $this->setClass($array['class']);
        }
        if (array_key_exists('config', $array)) {
            $this->setConfig($array['config']);
        }
    }

    /**
     * Get metadata information as array
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            'name'   => $this->getName(),
            'class'  => $this->getClass(),
            'config' => $this->getConfig(),
        );
    }

}
