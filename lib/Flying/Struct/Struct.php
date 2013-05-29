<?php

namespace Flying\Struct;

use Flying\Config\AbstractConfig;
use Flying\Struct\Common\StructItemInterface;
use Flying\Struct\Common\UpdateNotifyListenerInterface;
use Flying\Struct\Metadata\MetadataInterface;
use Flying\Struct\Metadata\StructMetadata;
use Flying\Struct\Property\PropertyInterface;

/**
 * Base implementation of structure class
 */
class Struct extends AbstractConfig implements StructInterface
{
    /**
     * Structure contents
     * @var array
     */
    protected $_struct;
    /**
     * Initial contents for structure properties
     * @var array
     */
    protected $_initialContents;
    /**
     * TRUE to skip property change notification, FALSE otherwise
     * @var boolean
     */
    protected $_skipNotify = false;
    /**
     * Structure size (for Countable interface)
     * @var int
     */
    protected $_count = 0;
    /**
     * Current index in structure (for Iterator interface)
     * @var int
     */
    protected $_index = 0;

    /**
     * Class constructor
     *
     * @param array|object $contents    OPTIONAL Contents to initialize structure with
     * @param array|object $config      OPTIONAL Configuration for this structure
     * @return Struct
     */
    public function __construct($contents = null, $config = null)
    {
        // No change notification is required during object construction
        $flag = $this->_skipNotify;
        $this->_skipNotify = true;
        $this->setConfig($config);
        if ($contents !== null) {
            $this->setInitialContents($contents);
        }
        $this->createStruct($this->getMetadata());
        $this->_skipNotify = $flag;
    }

    /**
     * Get structure metadata
     *
     * @throws Exception
     * @return StructMetadata
     */
    protected function getMetadata()
    {
        /** @var $metadata StructMetadata */
        $metadata = $this->getConfig('metadata');
        if (!$metadata instanceof StructMetadata) {
            /** @var $configuration Configuration */
            $configuration = $this->getConfig('configuration');
            $metadata = $configuration->getMetadataManager()->getMetadata($this);
            if (!$metadata instanceof StructMetadata) {
                throw new Exception('No metadata information is found for structure: ' . get_class($this));
            }
        }
        return $metadata;
    }

    /**
     * Get initial structure contents
     *
     * @param string $name      OPTIONAL Structure property name to get contents of,
     *                          NULL to get all available contents
     * @return mixed
     */
    protected function getInitialContents($name = null)
    {
        if (!is_array($this->_initialContents)) {
            $this->_initialContents = array();
        }
        if ($name !== null) {
            return (array_key_exists($name, $this->_initialContents)) ? $this->_initialContents[$name] : null;
        } else {
            return $this->_initialContents;
        }
    }

    /**
     * Set initial structure contents
     *
     * @param array|object $contents
     * @return void
     */
    protected function setInitialContents($contents)
    {
        if (is_array($contents)) {
            $this->_initialContents = $contents;
        } elseif (is_object($contents)) {
            $this->_initialContents = $this->convertToArray($contents);
        }
        if (!is_array($this->_initialContents)) {
            $this->_initialContents = array();
        }
    }

    /**
     * Attempt to convert given structure contents to array
     *
     * @param mixed $contents     Value to convert to array
     * @return array
     */
    protected function convertToArray($contents)
    {
        if (is_object($contents)) {
            if (is_callable(array($contents, 'toArray'))) {
                $contents = $contents->toArray();
            } elseif ($contents instanceof \ArrayObject) {
                $contents = $contents->getArrayCopy();
            } elseif ($contents instanceof \Iterator) {
                $temp = array();
                foreach ($contents as $k => $v) {
                    $temp[$k] = $v;
                }
                $contents = $temp;
            }
        }
        if (!is_array($contents)) {
            $contents = array();
        }
        return $contents;
    }

    /**
     * Create structure from given metadata information
     *
     * @param StructMetadata $metadata
     * @throws Exception
     * @return void
     */
    protected function createStruct(StructMetadata $metadata = null)
    {
        if (!$metadata) {
            $metadata = $this->getMetadata();
        }
        $contents = $this->getInitialContents();
        $baseConfig = array(
            'parent_structure'       => $this,
            'update_notify_listener' => $this,
        );
        $this->_struct = array();
        /** @var $property MetadataInterface */
        foreach ($metadata->getProperties() as $name => $property) {
            $class = $property->getClass();
            $value = (array_key_exists($name, $contents)) ? $contents[$name] : null;
            $config = array_merge($property->getConfig(), $baseConfig);
            $instance = new $class($value, $config);
            if ((!$instance instanceof PropertyInterface) && (!$instance instanceof StructInterface)) {
                throw new Exception('Invalid class "' . $class . '" for structure property: ' . $name);
            }
            $this->_struct[$name] = $instance;
        }
        $this->_count = sizeof($this->_struct);
        $this->rewind();
    }

    /**
     * Retrieve value of structure property with given name and return $default if there is no such property
     *
     * @param string $name      Structure property name to get value of
     * @param mixed $default    OPTIONAL Default value to return in a case if property is not available
     * @return mixed
     */
    public function get($name, $default = null)
    {
        $result = $default;
        if (array_key_exists($name, $this->_struct)) {
            $property = $this->_struct[$name];
            if ($property instanceof PropertyInterface) {
                $result = $property->get();
            } elseif ($property instanceof StructInterface) {
                $result = $property;
            }
        } else {
            $result = $this->getMissed($name, $default);
        }
        return $result;
    }

    /**
     * Handle get requests for missed structure properties
     *
     * @param string $name      Requested structure property name
     * @param mixed $default    Given default value
     * @return mixed
     */
    protected function getMissed($name, $default)
    {
        // This method can be overridden into derived classes
        // to handle attempts to get missed structure properties
        return $default;
    }

    /**
     * Magic function so that $obj->value will work.
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return ($this->get($name));
    }

    /**
     * Set value of structure property with given name
     *
     * @param string|array $name    Either name of structure property to set value of
     *                              or array of structure properties to set
     * @param mixed $value          OPTIONAL New value for this property (only if $name is a string)
     * @return void
     */
    public function set($name, $value = null)
    {
        $values = (is_scalar($name)) ? array($name => $value) : $name;
        foreach ($values as $k => $v) {
            if (!array_key_exists($k, $this->_struct)) {
                $this->setMissed($k, $v);
                continue;
            }
            $property = $this->_struct[$k];
            if ($property instanceof PropertyInterface) {
                $property->set($v);
            } elseif ($property instanceof StructInterface) {
                $property->set($v);
                $this->updateNotify($property);
            }
            $this->onChange($k);
        }
    }

    /**
     * Magic function for setting structure property value
     *
     * @param string $name      Structure property name to set value of
     * @param mixed $value      New value for this property
     * @return void
     */
    public function __set($name, $value)
    {
        $this->set($name, $value);
    }

    /**
     * Handle set requests for missed structure properties
     *
     * @param string $name    Structure property name to set
     * @param mixed $value    Given value for the property
     * @return void
     */
    protected function setMissed($name, $value)
    {
        // This method can be overridden into derived classes
        // to handle attempts to set missed structure properties
    }

    /**
     * Structure properties change event handler
     *
     * @param string $name  Name of changed property
     * @return void
     */
    protected function onChange($name)
    {
        // This method can be overridden into derived classes
        // to perform some additional tasks upon structure's properties change
    }

    /**
     * Reset structure to its initial state
     *
     * @return void
     */
    public function reset()
    {
        $flag = $this->_skipNotify;
        $this->_skipNotify = true;
        /** @var $property PropertyInterface */
        foreach ($this->_struct as $property) {
            $property->reset();
        }
        $this->_skipNotify = $flag;
        $this->rewind();
    }

    /**
     * Get structure contents as associative array
     *
     * @return array
     */
    public function toArray()
    {
        $array = array();
        foreach ($this->_struct as $key => $value) {
            if ($value instanceof PropertyInterface) {
                $array[$key] = $value->get();
            } elseif ($value instanceof StructInterface) {
                $array[$key] = $value->toArray();
            }
        }
        return ($array);
    }

    /**
     * Handle notification about update of given property
     *
     * @param StructItemInterface $property
     * @return void
     */
    public function updateNotify(StructItemInterface $property)
    {
        if (!$this->_skipNotify) {
            $owner = $this->getConfig('update_notify_listener');
            if ($owner instanceof UpdateNotifyListenerInterface) {
                $owner->updateNotify($property);
            }
        }
    }

    /**
     * Initialize list of configuration options
     *
     * @return void
     */
    protected function initConfig()
    {
        parent::initConfig();
        $this->mergeConfig(array(
            'configuration'          => null, // Structures configuration object (@see Configuration)
            'metadata'               => null, // Structure metadata
            'parent_structure'       => null, // Link to parent structure in a case of multi-level structures
            'update_notify_listener' => null, // Listener for structure's update notifications
        ));
    }

    /**
     * Perform "lazy initialization" of configuration option with given name
     *
     * @param string $name          Configuration option name
     * @return mixed
     */
    protected function lazyConfigInit($name)
    {
        switch ($name) {
            case 'configuration':
                return ConfigurationManager::getConfiguration();
                break;
            case 'metadata':
                /** @var $configuration Configuration */
                $configuration = $this->getConfig('configuration');
                return $configuration->getMetadataManager()->getMetadata($this);
                break;
            default:
                return parent::lazyConfigInit($name);
                break;
        }
    }

    /**
     * Check that given value of configuration option is valid
     *
     * @param string $name          Configuration option name
     * @param mixed $value          Option value (passed by reference)
     * @throws \InvalidArgumentException
     * @return boolean
     */
    protected function validateConfig($name, &$value)
    {
        switch ($name) {
            case 'configuration':
                if (($value !== null) && (!$value instanceof Configuration)) {
                    throw new \InvalidArgumentException('Structure configuration object must be instance of Configuration');
                }
                break;
            case 'metadata':
                if (($value !== null) && (!$value instanceof StructMetadata)) {
                    throw new \InvalidArgumentException('Structure metadata object must be instance of StructMetadata');
                }
                break;
            case 'parent_structure':
                if (($value !== null) && (!$value instanceof StructInterface)) {
                    throw new \InvalidArgumentException('Only structure object can be used as parent structure');
                }
                break;
            case 'update_notify_listener':
                if (($value !== null) && (!$value instanceof UpdateNotifyListenerInterface)) {
                    throw new \InvalidArgumentException('Update notification listener must implement UpdateNotifyListenerInterface interface');
                }
                break;
        }
        return true;
    }

    /**
     * Support isset() overloading
     *
     * @param string $name
     * @return boolean
     */
    public function __isset($name)
    {
        return (array_key_exists($name, $this->_struct));
    }

    /**
     * Support unset() overloading
     * Unset of structure property in a term of removing it from structure is not allowed,
     * so unset() just reset field's value.
     *
     * @param  string $name
     * @return void
     */
    public function __unset($name)
    {
        if (array_key_exists($name, $this->_struct)) {
            /** @var $property PropertyInterface */
            $property = $this->_struct[$name];
            $property->reset();
        }
    }

    /**
     * Defined by Countable interface
     *
     * @return int
     */
    public function count()
    {
        return ($this->_count);
    }

    /**
     * Defined by Iterator interface
     *
     * @return mixed
     */
    public function current()
    {
        return ($this->get(key($this->_struct)));
    }

    /**
     * Defined by Iterator interface
     *
     * @return mixed
     */
    public function key()
    {
        return (key($this->_struct));
    }

    /**
     * Defined by Iterator interface
     *
     * @return void
     */
    public function next()
    {
        next($this->_struct);
        $this->_index++;
    }

    /**
     * Defined by Iterator interface
     *
     * @return void
     */
    public function rewind()
    {
        reset($this->_struct);
        $this->_index = 0;
    }

    /**
     * Defined by Iterator interface
     *
     * @return boolean
     */
    public function valid()
    {
        return ($this->_index < $this->_count);
    }

    /**
     * Defined by RecursiveIterator interface
     *
     * @return boolean
     */
    public function hasChildren()
    {
        return ($this->_struct[key($this->_struct)] instanceof StructInterface);
    }

    /**
     * Defined by RecursiveIterator interface
     *
     * @return \RecursiveIterator
     */
    public function getChildren()
    {
        return ($this->_struct[key($this->_struct)]);
    }

    /**
     * Defined by ArrayAccess interface
     *
     * @param mixed $offset
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return ($this->__isset($offset));
    }

    /**
     * Defined by ArrayAccess interface
     *
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return ($this->get($offset));
    }

    /**
     * Defined by ArrayAccess interface
     *
     * @param mixed $offset
     * @param mixed $value
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * Defined by ArrayAccess interface
     *
     * @param mixed $offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        $this->__unset($offset);
    }

    /**
     * Implementation of Serializable interface
     *
     * @return string
     */
    public function serialize()
    {
        return (serialize($this->_struct));
    }

    /**
     * Implementation of Serializable interface
     *
     * @param array $data   Serialized object data
     * @return void
     */
    public function unserialize($data)
    {
        $this->createStruct();
        $data = @unserialize($data);
        if (!is_array($data)) {
            return;
        }
        foreach ($data as $name => $value) {
            if (array_key_exists($name, $this->_struct)) {
                $this->_struct[$name] = $value;
            }
        }
    }

}
