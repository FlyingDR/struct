<?php

namespace Flying\Struct;

use Flying\Config\AbstractConfig;
use Flying\Struct\Common\ComplexPropertyInterface;
use Flying\Struct\Common\SimplePropertyInterface;
use Flying\Struct\Common\UpdateNotifyListenerInterface;
use Flying\Struct\Metadata\MetadataInterface;
use Flying\Struct\Metadata\MetadataModificationInterface;
use Flying\Struct\Metadata\StructMetadata;
use Flying\Struct\Property\Property;
use Flying\Struct\Property\PropertyInterface;

/**
 * Base implementation of structure class
 */
class Struct extends AbstractConfig implements StructInterface, MetadataModificationInterface
{
    /**
     * Structure contents
     *
     * @var array
     */
    private $struct;
    /**
     * Initial contents for structure properties
     *
     * @var array
     */
    protected $initialContents;
    /**
     * TRUE to skip property change notification, FALSE otherwise
     *
     * @var boolean
     */
    protected $skipNotify = false;
    /**
     * Structure size (for Countable interface)
     *
     * @var int
     */
    private $count = 0;
    /**
     * Current index in structure (for Iterator interface)
     *
     * @var int
     */
    private $index = 0;
    /**
     * Structure metadata
     *
     * @var StructMetadata
     */
    private $metadata;
    /**
     * Parent structure or NULL if this is top-level structure
     *
     * @var Struct
     */
    protected $parent;

    /**
     * Class constructor
     *
     * @param array|object $contents OPTIONAL Contents to initialize structure with
     * @param array|object $config   OPTIONAL Configuration for this structure
     * @return Struct
     */
    public function __construct($contents = null, $config = null)
    {
        // No change notification is required during object construction
        $flag = $this->skipNotify;
        $this->skipNotify = true;
        $this->setConfig($config);
        if ($contents !== null) {
            $this->setInitialContents($contents);
        }
        $this->createStruct($this->getMetadata());
        $this->skipNotify = $flag;
    }

    /**
     * Handling of object cloning
     *
     * @return void
     */
    public function __clone()
    {
        $config = array(
            'parent_structure'       => $this,
            'update_notify_listener' => $this,
        );
        /** @var $property Property */
        foreach ($this->struct as &$property) {
            $property = clone $property;
            $property->setConfig($config);
        }
    }

    /**
     * Get structure metadata
     *
     * @throws Exception
     * @return StructMetadata
     */
    protected function getMetadata()
    {
        if (!$this->metadata) {
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
            $this->metadata = $metadata;
        }
        return $this->metadata;
    }

    /**
     * Modify metadata for this structure after it was parsed by MetadataManager
     *
     * @param StructMetadata $metadata
     */
    public static function modifyMetadata(StructMetadata $metadata)
    {

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
        if (!is_array($this->initialContents)) {
            $this->initialContents = array();
        }
        if ($name !== null) {
            return (array_key_exists($name, $this->initialContents)) ? $this->initialContents[$name] : null;
        } else {
            return $this->initialContents;
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
            $this->initialContents = $contents;
        } elseif (is_object($contents)) {
            $this->initialContents = $this->convertToArray($contents);
        }
        if (!is_array($this->initialContents)) {
            $this->initialContents = array();
        }
    }

    /**
     * Attempt to convert given structure contents to array
     *
     * @param mixed $contents Value to convert to array
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
        $this->struct = array();
        /** @var $property MetadataInterface */
        foreach ($metadata->getProperties() as $name => $property) {
            $class = $property->getClass();
            $value = (array_key_exists($name, $contents)) ? $contents[$name] : null;
            $config = array_merge($property->getConfig(), $baseConfig);
            if ($property instanceof StructMetadata) {
                $config['metadata'] = $property;
            }
            if ($class === null) {
                // Structure properties metadata is defined explicitly
                $class = $this->getConfig('explicit_metadata_class');
                $config['metadata'] = $property;
            }
            $instance = new $class($value, $config);
            if ((!$instance instanceof PropertyInterface) && (!$instance instanceof StructInterface)) {
                throw new Exception('Invalid class "' . $class . '" for structure property: ' . $name);
            }
            $this->struct[$name] = $instance;
        }
        $this->count = count($this->struct);
        $this->rewind();
    }

    /**
     * Retrieve value of structure property with given name and return $default if there is no such property
     *
     * @param string $name   Structure property name to get value of
     * @param mixed $default OPTIONAL Default value to return in a case if property is not available
     * @return mixed
     */
    public function get($name, $default = null)
    {
        $result = $default;
        if (array_key_exists($name, $this->struct)) {
            $property = $this->struct[$name];
            if ($property instanceof ComplexPropertyInterface) {
                $result = $property;
            } elseif ($property instanceof PropertyInterface) {
                $result = $property->getValue();
            }
        } else {
            $result = $this->getMissed($name, $default);
        }
        return $result;
    }

    /**
     * Get structure property with given name
     *
     * @param string $name
     * @return PropertyInterface|ComplexPropertyInterface|null
     */
    public function getProperty($name)
    {
        /** @noinspection ImplicitMagicMethodCallInspection */
        if ($this->__isset($name)) {
            return $this->struct[$name];
        }
        return null;
    }

    /**
     * Handle get requests for missed structure properties
     *
     * @param string $name   Requested structure property name
     * @param mixed $default Given default value
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
            if (!array_key_exists($k, $this->struct)) {
                $this->setMissed($k, $v);
                continue;
            }
            $property = $this->struct[$k];
            if ($property instanceof PropertyInterface) {
                $property->setValue($v);
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
     * @param string $name Structure property name to set value of
     * @param mixed $value New value for this property
     * @return void
     */
    public function __set($name, $value)
    {
        $this->set($name, $value);
    }

    /**
     * Handle set requests for missed structure properties
     *
     * @param string $name Structure property name to set
     * @param mixed $value Given value for the property
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
     * @param string $name Name of changed property
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
        $flag = $this->skipNotify;
        $this->skipNotify = true;
        /** @var $property PropertyInterface */
        foreach ($this->struct as $property) {
            $property->reset();
        }
        $this->skipNotify = $flag;
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
        foreach ($this->struct as $key => $value) {
            if ($value instanceof ComplexPropertyInterface) {
                $array[$key] = $value->toArray();
            } elseif ($value instanceof PropertyInterface) {
                $array[$key] = $value->getValue();
            }
        }
        return ($array);
    }

    /**
     * Handle notification about update of given property
     *
     * @param SimplePropertyInterface $property
     * @return void
     */
    public function updateNotify(SimplePropertyInterface $property)
    {
        if (!$this->skipNotify) {
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
     * @throws \InvalidArgumentException
     */
    protected function initConfig()
    {
        parent::initConfig();
        $this->mergeConfig(array(
            'configuration'           => null, // Structures configuration object (@see Configuration)
            'metadata'                => null, // Structure metadata
            'parent_structure'        => null, // Link to parent structure in a case of multi-level structures
            'update_notify_listener'  => null, // Listener for structure's update notifications
            'explicit_metadata_class' => null, // Name of the class to use to create structures from explicitly defined properties
        ));
    }

    /**
     * Perform "lazy initialization" of configuration option with given name
     *
     * @param string $name Configuration option name
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
            case 'explicit_metadata_class':
                return __CLASS__;
            default:
                return parent::lazyConfigInit($name);
                break;
        }
    }

    /**
     * Check that given value of configuration option is valid
     *
     * @param string $name Configuration option name
     * @param mixed $value Option value (passed by reference)
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
            case 'explicit_metadata_class':
                /** @noinspection ReferenceMismatchInspection */
                if (!is_string($value)) {
                    throw new \InvalidArgumentException('Explicit metadata class name should be defined as string');
                }
                if (!class_exists($value)) {
                    throw new \InvalidArgumentException('Unknown class name is defined for explicit metadata class');
                }
                $reflection = new \ReflectionClass($value);
                if ((!$reflection->isInstantiable()) || (!$reflection->implementsInterface('Flying\Struct\StructInterface'))) {
                    throw new \InvalidArgumentException('Invalid class is defined for explicit metadata class');
                }
                break;
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function onConfigChange($name, $value)
    {
        switch ($name) {
            case 'metadata':
                $this->metadata = $value;
                break;
            case 'parent_structure':
                $this->parent = $value;
                break;
        }
        parent::onConfigChange($name, $value);
    }

    /**
     * Support isset() overloading
     *
     * @param string $name
     * @return boolean
     */
    public function __isset($name)
    {
        return (array_key_exists($name, $this->struct));
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
        if (array_key_exists($name, $this->struct)) {
            /** @var $property PropertyInterface */
            $property = $this->struct[$name];
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
        return ($this->count);
    }

    /**
     * Defined by Iterator interface
     *
     * @return mixed
     */
    public function current()
    {
        return ($this->get(key($this->struct)));
    }

    /**
     * Defined by Iterator interface
     *
     * @return mixed
     */
    public function key()
    {
        return (key($this->struct));
    }

    /**
     * Defined by Iterator interface
     *
     * @return void
     */
    public function next()
    {
        next($this->struct);
        $this->index++;
    }

    /**
     * Defined by Iterator interface
     *
     * @return void
     */
    public function rewind()
    {
        reset($this->struct);
        $this->index = 0;
    }

    /**
     * Defined by Iterator interface
     *
     * @return boolean
     */
    public function valid()
    {
        return ($this->index < $this->count);
    }

    /**
     * Defined by RecursiveIterator interface
     *
     * @return boolean
     */
    public function hasChildren()
    {
        return ($this->struct[key($this->struct)] instanceof StructInterface);
    }

    /**
     * Defined by RecursiveIterator interface
     *
     * @return \RecursiveIterator
     */
    public function getChildren()
    {
        return ($this->struct[key($this->struct)]);
    }

    /**
     * Defined by ArrayAccess interface
     *
     * @param mixed $offset
     * @return boolean
     */
    public function offsetExists($offset)
    {
        /** @noinspection ImplicitMagicMethodCallInspection */
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
        /** @noinspection ImplicitMagicMethodCallInspection */
        $this->__unset($offset);
    }

    /**
     * Implementation of Serializable interface
     *
     * @return string
     */
    public function serialize()
    {
        return (serialize(array(
            'metadata' => $this->getMetadata(),
            'struct'   => $this->toArray(),
        )));
    }

    /**
     * Implementation of Serializable interface
     *
     * @param array $data Serialized object data
     * @throws \InvalidArgumentException
     * @return void
     */
    public function unserialize($data)
    {
        $data = unserialize($data);
        if ((!is_array($data)) ||
            (!array_key_exists('metadata', $data)) ||
            (!$data['metadata'] instanceof StructMetadata) ||
            (!array_key_exists('struct', $data)) ||
            (!is_array($data['struct']))
        ) {
            throw new \InvalidArgumentException('Serialized structure information has invalid format');
        }
        $flag = $this->skipNotify;
        $this->skipNotify = true;
        $this->setConfig('metadata', $data['metadata']);
        $this->createStruct();
        $this->set($data['struct']);
        $this->skipNotify = $flag;
    }
}
