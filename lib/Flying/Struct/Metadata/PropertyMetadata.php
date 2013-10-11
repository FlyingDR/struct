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
    protected $name;
    /**
     * Class name for property object
     * @var string
     */
    protected $class;
    /**
     * Hash for property object
     * @var string
     */
    protected $hash;
    /**
     * Configuration options for property object
     * @var array
     */
    protected $config = array();

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
        return $this->name;
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
        $this->name = $name;
        $this->hash = null;
        return $this;
    }

    /**
     * Get class name for property object
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
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
        $this->class = $class;
        $this->hash = null;
        return $this;
    }

    /**
     * Get configuration options for property object
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Set configuration options for property object
     *
     * @param array $config
     * @return $this
     */
    public function setConfig(array $config)
    {
        $this->config = $config;
        $this->hash = null;
        return $this;
    }

    /**
     * Defined by Serializable interface
     *
     * @return string
     */
    public function serialize()
    {
        return (serialize(array(
             'name'   => $this->getName(),
             'class'  => $this->getClass(),
             'config' => $this->getConfig(),
        )));
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
            'hash'   => $this->getHash(),
        );
    }

    /**
     * Get hash for this structure metadata item
     *
     * @return string
     */
    public function getHash()
    {
        if (!$this->hash) {
            $this->hash = sha1($this->getName() . $this->getClass() . serialize($this->getConfig()));
        }
        return $this->hash;
    }
}
