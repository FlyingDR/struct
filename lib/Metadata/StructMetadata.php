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
     *
     * @var array
     */
    private $properties = [];

    /**
     * Class constructor
     *
     * @param string $name      OPTIONAL Property name
     * @param string $class     OPTIONAL Class name for property object
     * @param array $config     OPTIONAL Configuration options for property object
     * @param array $properties OPTIONAL Structure properties
     * @throws \InvalidArgumentException
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
     * @param string $name Property name
     * @return boolean
     */
    public function hasProperty($name)
    {
        return array_key_exists($name, $this->properties);
    }

    /**
     * Get metadata for structure property with given name
     *
     * @param string $name Property name
     * @throws Exception
     * @return MetadataInterface
     */
    public function getProperty($name)
    {
        if (!array_key_exists($name, $this->properties)) {
            throw new Exception('No metadata is available for property: ' . $name);
        }
        return $this->properties[$name];
    }

    /**
     * Add given metadata as structure property
     *
     * @param MetadataInterface $metadata Property metadata
     * @return $this
     */
    public function addProperty(MetadataInterface $metadata)
    {
        $this->properties[$metadata->getName()] = $metadata;
        $this->hash = null;
        return $this;
    }

    /**
     * Remove structure property with given name
     *
     * @param string $name Property name
     * @return $this
     */
    public function removeProperty($name)
    {
        unset($this->properties[$name]);
        $this->hash = null;
        return $this;
    }

    /**
     * Clear all registered structure properties
     *
     * @return $this
     */
    public function clearProperties()
    {
        $this->properties = [];
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
        return serialize([
            'name'       => $this->getName(),
            'class'      => $this->getClass(),
            'config'     => $this->getConfig(),
            'properties' => $this->getProperties(),
        ]);
    }

    /**
     * Get metadata for all registered structure properties
     *
     * @return array
     */
    public function getProperties()
    {
        return $this->properties;
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
     * Defined by Serializable interface
     *
     * @param string $serialized
     * @return void
     * @throws \InvalidArgumentException
     */
    public function unserialize($serialized)
    {
        $array = unserialize($serialized);
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

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        $array = [
            'name'       => $this->getName(),
            'class'      => $this->getClass(),
            'config'     => $this->getConfig(),
            'hash'       => $this->getHash(),
            'properties' => [],
        ];
        /** @var $property MetadataInterface */
        foreach ($this->getProperties() as $name => $property) {
            $array['properties'][$name] = $property->toArray();
        }
        return $array;
    }

    /**
     * {@inheritdoc}
     */
    public function getHash()
    {
        if (!$this->hash) {
            $hash = [
                $this->getName(),
                $this->getClass(),
                serialize($this->getConfig()),
            ];
            /** @var $property PropertyMetadata */
            foreach ($this->getProperties() as $property) {
                $hash[] = $property->getHash();
            }
            $this->hash = sha1(implode('|', $hash));
        }
        return $this->hash;
    }
}
