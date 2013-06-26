<?php

namespace Flying\Struct;

use Flying\Struct\Metadata\StructMetadata;
use Flying\Struct\Storage\StorableInterface;
use Flying\Struct\Storage\Storage;
use Flying\Struct\Storage\StorageInterface;

/**
 * Implementation of structure with ability to store its state into storage
 */
class StorableStruct extends Struct implements StorableInterface
{
    /**
     * Structures storage
     * @var Storage
     */
    protected $_storage = null;
    /**
     * Storage key for this structure
     * @var string
     */
    protected $_storageKey = null;
    /**
     * TRUE if structure is already marked as "dirty" into storage
     * @var boolean
     */
    protected $_markedAsDirty = false;

    /**
     * Class constructor
     *
     * @param array|object $contents    OPTIONAL Contents to initialize structure with
     * @param array|object $config      OPTIONAL Configuration for this structure
     * @return StorableStruct
     */
    public function __construct($contents = null, $config = null)
    {
        // Structure should be initialized with its stored contents
        parent::__construct(null, $config);
        // No change notification is required during object construction
        $flag = $this->_skipNotify;
        $this->_skipNotify = true;
        if (is_object($contents)) {
            $contents = $this->convertToArray($contents);
        }
        if (is_array($contents)) {
            $this->set($contents);
        }
        $this->_skipNotify = $flag;
    }

    /**
     * Get key for this structure to use in structures storage
     *
     * @return string
     */
    public function getStorageKey()
    {
        // Child structures should not be stored separately
        if ($this->_parent) {
            return null;
        }
        if (!$this->_storageKey) {
            $class = get_class($this);
            $hash = sha1(serialize($this->getMetadata()));
            $this->_storageKey = $class . '_' . $hash;
        }
        return $this->_storageKey;
    }

    /**
     * Get object representation suitable to put into storage
     *
     * @return mixed
     */
    public function toStorage()
    {
        return $this->toArray();
    }

    /**
     * Get storage container
     *
     * @throws Exception
     * @return StorageInterface
     */
    protected function getStorage()
    {
        if (!$this->_storage) {
            /** @var $storage StorageInterface */
            $storage = $this->getConfig('storage');
            if (!$storage instanceof StorageInterface) {
                /** @var $configuration Configuration */
                $configuration = $this->getConfig('configuration');
                $storage = $configuration->getStorage();
                if (!$storage instanceof StorageInterface) {
                    throw new Exception('No storage is available');
                }
            }
            $this->_storage = $storage;
        }
        return $this->_storage;
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
            // Initial contents for structure are taken from storage
            $contents = $this->getStorage()->load($this->getStorageKey());
            if (!is_array($contents)) {
                $contents = array();
            }
            $this->_initialContents = $contents;
        }
        return parent::getInitialContents($name);
    }

    /**
     * Set initial structure contents
     *
     * @param array|object $contents
     * @return void
     */
    protected function setInitialContents($contents)
    {
        // Given initial contents should be ignored
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
        parent::createStruct($metadata);
        // Register ourselves into storage, but only top-level structure should be stored
        if (!$this->_parent) {
            $this->getStorage()->register($this);
            $this->_markedAsDirty = false;
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
            'storage' => null,
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
            case 'storage':
                /** @var $configuration Configuration */
                $configuration = $this->getConfig('configuration');
                return $configuration->getStorage();
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
            case 'storage':
                if (($value !== null) && (!$value instanceof StorageInterface)) {
                    throw new \InvalidArgumentException('Structure storage object must be instance of StorageInterface');
                }
                break;
            default:
                return parent::validateConfig($name, $value);
                break;
        }
        return true;
    }

    /**
     * Perform required operations when configuration option value is changed
     *
     * @param string $name          Configuration option name
     * @param mixed $value          Configuration option value
     * @param boolean $merge        TRUE if configuration option is changed during merge process,
     *                              FALSE if it is changed by setting configuration option
     * @return void
     */
    protected function onConfigChange($name, $value, $merge)
    {
        switch ($name) {
            case 'storage':
                $this->_storage = $value;
                break;
            case 'configuration':
            case 'metadata':
                // Since storage key depends on structure's metadata -
                // it should be reset upon metadata change
                $this->_storageKey = null;
                break;
            case 'parent_structure':
                $this->_parent = $value;
                break;
        }
        parent::onConfigChange($name, $value, $merge);
    }

    /**
     * Structure properties change event handler
     *
     * @param string $name  Name of changed property
     * @return void
     */
    protected function onChange($name)
    {
        if ($this->_parent) {
            $this->_parent->onChange($name);
        } elseif (!$this->_markedAsDirty) {
            $this->getStorage()->markAsDirty($this);
            $this->_markedAsDirty = true;
        }
    }

}
