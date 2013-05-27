<?php

namespace Flying\Struct\Metadata;

use Flying\Struct\Exception;

/**
 * Structure metadata storage class
 */
class StructMetadata extends PropertyMetadata
{
    /**
     * Structure properties
     * @var array
     */
    protected $_properties = array();

    /**
     * Class constructor
     *
     * @param string $name          OPTIONAL Property name
     * @param string $class         OPTIONAL Class name for property object
     * @param array $config         OPTIONAL Configuration options for property object
     * @param array $properties     OPTIONAL Structure properties
     * @return StructMetadata
     */
    public function __construct($name = null, $class = null, $config = null, $properties = null)
    {
        parent::__construct($name, $class, $config);
        if ($properties !== null) {
            $this->setProperties($properties);
        }
    }

    /**
     * Check if structure property with given name is available
     *
     * @param string $name  Property name
     * @return boolean
     */
    public function hasProperty($name)
    {
        return (array_key_exists($name, $this->_properties));
    }

    /**
     * Get metadata for structure property with given name
     *
     * @param string $name  Property name
     * @throws Exception
     * @return MetadataInterface
     */
    public function getProperty($name)
    {
        if (!array_key_exists($name, $this->_properties)) {
            throw new Exception('No metadata is available for property: ' . $name);
        }
        return ($this->_properties[$name]);
    }

    /**
     * Get metadata for all registered structure properties
     *
     * @return array
     */
    public function getProperties()
    {
        return ($this->_properties);
    }

    /**
     * Add given metadata as structure property
     *
     * @param MetadataInterface $metadata   Property metadata
     * @return $this
     */
    public function addProperty(MetadataInterface $metadata)
    {
        $this->_properties[$metadata->getName()] = $metadata;
        return $this;
    }

    /**
     * Set structure properties metadata
     *
     * @param array $properties
     * @return $this
     */
    public function setProperties(array $properties)
    {
        $this->clearProperties();
        foreach ($properties as $metadata) {
            $this->addProperty($metadata);
        }
        return $this;
    }

    /**
     * Remove structure property with given name
     *
     * @param string $name      Property name
     * @return $this
     */
    public function removeProperty($name)
    {
        unset($this->_properties[$name]);
        return $this;
    }

    /**
     * Clear all registered structure properties
     *
     * @return $this
     */
    public function clearProperties()
    {
        $this->_properties = array();
    }

    /**
     * Defined by Serializable interface
     *
     * @return string
     */
    public function serialize()
    {
        return (serialize(array(
            'name'       => $this->getName(),
            'class'      => $this->getClass(),
            'config'     => $this->getConfig(),
            'properties' => $this->getProperties(),
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
        if (array_key_exists('properties', $array)) {
            $this->setProperties($array['properties']);
        }
    }

    public function toArray()
    {
        $array = array(
            'name'       => $this->getName(),
            'class'      => $this->getClass(),
            'config'     => $this->getConfig(),
            'properties' => array(),
        );
        /** @var $property MetadataInterface */
        foreach ($this->getProperties() as $name => $property) {
            $array['properties'][$name] = $property->toArray();
        }
        return $array;
    }

}