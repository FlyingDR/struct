<?php

namespace Flying\Struct;

use Flying\Struct\Common\SimplePropertyInterface;
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
     * TRUE if structure is already marked as "dirty" into storage
     *
     * @var boolean
     */
    protected $markedAsDirty = false;
    /**
     * Structures storage
     *
     * @var Storage
     */
    private $storage;
    /**
     * Storage key for this structure
     *
     * @var string
     */
    private $storageKey;

    /**
     * Class constructor
     *
     * @param array|object $contents OPTIONAL Contents to initialize structure with
     * @param array|object $config   OPTIONAL Configuration for this structure
     * @throws \Flying\Struct\Exception
     * @throws \RuntimeException
     */
    public function __construct($contents = null, $config = null)
    {
        // Structure should be initialized with its stored contents
        parent::__construct(null, $config);
        // No change notification is required during object construction
        $flag = $this->skipNotify;
        $this->skipNotify = true;
        if (is_object($contents)) {
            $contents = $this->convertToArray($contents);
        }
        if (is_array($contents)) {
            $this->set($contents);
        }
        $this->skipNotify = $flag;
    }

    public function __clone()
    {
        parent::__clone();
        // Register newly cloned object into storage
        if ($this->getStorageKey() !== null) {
            $this->getStorage()->register($this);
            // If structure had some changes before cloning - its cloned version should also be marked as "dirty"
            if ($this->markedAsDirty) {
                $this->getStorage()->markAsDirty($this);
            }
        }
    }

    /**
     * Get key for this structure to use in structures storage
     *
     * @return string|null
     * @throws \Flying\Struct\Exception
     */
    public function getStorageKey()
    {
        // Child structures should not be stored separately
        if ($this->parent) {
            return null;
        }
        if (!$this->storageKey) {
            $class = str_replace('\\', '_', get_class($this));
            $this->storageKey = $class . '_' . $this->getMetadata()->getHash();
        }
        return $this->storageKey;
    }

    /**
     * Get storage container
     *
     * @return StorageInterface
     * @throws \Flying\Struct\Exception
     */
    protected function getStorage()
    {
        if (!$this->storage) {
            try {
                /** @var $storage StorageInterface */
                $storage = $this->getConfig('storage');
                if (!$storage instanceof StorageInterface) {
                    /** @var $configuration Configuration */
                    $configuration = $this->getConfig('configuration');
                    $storage = $configuration->getStorage();
                    /** @noinspection NotOptimalIfConditionsInspection */
                    if (!$storage instanceof StorageInterface) {
                        throw new Exception('No storage is available');
                    }
                }
            } catch (\RuntimeException $e) {
                throw new Exception('Failed to obtain storage');
            }
            $this->storage = $storage;
        }
        return $this->storage;
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
     * {@inheritdoc}
     * @throws \Flying\Struct\Exception
     */
    public function updateNotify(SimplePropertyInterface $property)
    {
        parent::updateNotify($property);
        if ($this->markedAsDirty === false && $this->getStorageKey() !== null) {
            $this->getStorage()->markAsDirty($this);
            $this->markedAsDirty = true;
        }
    }

    /**
     * Get initial structure contents
     *
     * @param string $name      OPTIONAL Structure property name to get contents of,
     *                          NULL to get all available contents
     * @return mixed
     * @throws \Flying\Struct\Exception
     */
    protected function getInitialContents($name = null)
    {
        if (!is_array($this->initialContents)) {
            // Initial contents for structure are taken from storage
            $contents = [];
            $key = $this->getStorageKey();
            if ($key !== null) {
                // Storage key is only available for top-level structure so it may be missed
                $contents = $this->getStorage()->load($key);
                if (!is_array($contents)) {
                    $contents = [];
                }
            }
            $this->initialContents = $contents;
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
     * @throws \RuntimeException
     */
    protected function createStruct(StructMetadata $metadata = null)
    {
        parent::createStruct($metadata);
        // Register ourselves into storage, but only top-level structure should be stored
        if ($this->getStorageKey() !== null) {
            $this->getStorage()->register($this);
            $this->markedAsDirty = false;
        }
    }

    /**
     * {@inheritdoc}
     * @throws \RuntimeException
     */
    protected function initConfig()
    {
        parent::initConfig();
        $this->mergeConfig([
            'storage' => null,
        ]);
    }

    /**
     * Perform "lazy initialization" of configuration option with given name
     *
     * @param string $name Configuration option name
     * @return mixed
     * @throws \RuntimeException
     */
    protected function lazyConfigInit($name)
    {
        /** @noinspection DegradedSwitchInspection */
        switch ($name) {
            case 'storage':
                /** @var $configuration Configuration */
                $configuration = $this->getConfig('configuration');
                return $configuration->getStorage();
            default:
                return parent::lazyConfigInit($name);
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
        /** @noinspection DegradedSwitchInspection */
        switch ($name) {
            case 'storage':
                if (($value !== null) && (!$value instanceof StorageInterface)) {
                    throw new \InvalidArgumentException('Structure storage object must be instance of StorageInterface');
                }
                break;
            default:
                return parent::validateConfig($name, $value);
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function onConfigChange($name, $value)
    {
        switch ($name) {
            case 'storage':
                $this->storage = $value;
                break;
            case 'configuration':
            case 'metadata':
                // Since storage key depends on structure's metadata -
                // it should be reset upon metadata change
                $this->storageKey = null;
                break;
            case 'parent_structure':
                $this->parent = $value;
                break;
        }
        parent::onConfigChange($name, $value);
    }
}
