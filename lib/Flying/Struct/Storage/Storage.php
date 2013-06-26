<?php

namespace Flying\Struct\Storage;

use Flying\Struct\ConfigurationManager;
use Flying\Struct\Storage\StorableInterface;

/**
 * Objects storage container
 */
class Storage implements StorageInterface
{
    /**
     * List of stored objects
     * @var array
     */
    protected $_storage = array();
    /**
     * List of objects marked as "dirty"
     * @var array
     */
    protected $_dirty = array();
    /**
     * Storage backend
     * @var BackendInterface
     */
    protected $_backend;

    /**
     * Register given object in storage
     *
     * @param StorableInterface $object
     * @return $this
     */
    public function register(StorableInterface $object)
    {
        $hash = spl_object_hash($object);
        $this->_storage[$hash] = $object;
        return $this;
    }

    /**
     * Check if given object is registered into storage
     *
     * @param StorableInterface $object
     * @return boolean
     */
    public function has(StorableInterface $object)
    {
        $hash = spl_object_hash($object);
        return array_key_exists($hash, $this->_storage);
    }

    /**
     * Remove given object from storage
     *
     * @param StorableInterface $object
     * @return $this
     */
    public function remove(StorableInterface $object)
    {
        $hash = spl_object_hash($object);
        unset($this->_storage[$hash]);
        return $this;
    }

    /**
     * Load contents of given object from storage
     *
     * @param string $key   Storage key
     * @return mixed
     */
    public function load($key)
    {
        $backend = $this->getBackend();
        $result = null;
        if ($backend->has($key)) {
            $result = $backend->load($key);
        }
        return $result;
    }

    /**
     * Mark given object as "dirty"
     *
     * @param StorableInterface $object
     * @return $this
     */
    public function markAsDirty(StorableInterface $object)
    {
        $hash = spl_object_hash($object);
        $this->_dirty[$hash] = true;
        return $this;
    }

    /**
     * Mark given object as "not dirty"
     *
     * @param StorableInterface $object
     * @return $this
     */
    public function markAsNotDirty(StorableInterface $object)
    {
        $hash = spl_object_hash($object);
        unset($this->_dirty[$hash]);
        return $this;
    }

    /**
     * Check if given object is "dirty"
     *
     * @param StorableInterface $object
     * @return boolean
     */
    public function isDirty(StorableInterface $object)
    {
        $hash = spl_object_hash($object);
        return array_key_exists($hash, $this->_dirty);
    }

    /**
     * Get storage backend
     *
     * @return BackendInterface
     */
    public function getBackend()
    {
        if (!$this->_backend) {
            $this->_backend = ConfigurationManager::getConfiguration()->getStorageBackend();
        }
        return $this->_backend;
    }

    /**
     * Set storage backend
     *
     * @param BackendInterface $backend
     * @return $this
     */
    public function setBackend(BackendInterface $backend)
    {
        $this->_backend = $backend;
        return $this;
    }

    /**
     * Flush all changes in objects into storage
     *
     * @throws \RuntimeException
     * @return $this
     */
    public function flush()
    {
        $backend = $this->getBackend();
        $storedKeys = array();
        /** @var $object StorableInterface */
        foreach ($this->_storage as $hash => $object) {
            $key = $object->getStorageKey();
            if (($backend->has($key)) && (!array_key_exists($hash, $this->_dirty))) {
                continue;
            }
            if (in_array($key, $storedKeys)) {
                throw new \RuntimeException('Multiple objects with same storage key "' . $key . '" are requested to be stored');
            }
            $backend->save($key, $object->toStorage());
            $storedKeys[] = $key;
        }
        $this->_dirty = array();
        return $this;
    }

}