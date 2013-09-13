<?php

namespace Flying\Struct\Storage;

use Flying\Struct\ConfigurationManager;

/**
 * Interface for objects storage containers
 */
interface StorageInterface
{
    /**
     * Register given object in storage
     *
     * @param StorableInterface $object
     * @return $this
     */
    public function register(StorableInterface $object);

    /**
     * Check if given object is registered into storage
     *
     * @param StorableInterface $object
     * @return boolean
     */
    public function has(StorableInterface $object);

    /**
     * Remove given object from storage
     *
     * @param StorableInterface $object
     * @return $this
     */
    public function remove(StorableInterface $object);

    /**
     * Load contents of given object from storage
     *
     * @param string $key   Storage key
     * @return mixed
     */
    public function load($key);

    /**
     * Mark given object as "dirty"
     *
     * @param StorableInterface $object
     * @return $this
     */
    public function markAsDirty(StorableInterface $object);

    /**
     * Mark given object as "not dirty"
     *
     * @param StorableInterface $object
     * @return $this
     */
    public function markAsNotDirty(StorableInterface $object);

    /**
     * Check if given object is "dirty"
     *
     * @param StorableInterface $object
     * @return boolean
     */
    public function isDirty(StorableInterface $object);

    /**
     * Get storage backend
     *
     * @return BackendInterface
     */
    public function getBackend();

    /**
     * Set storage backend
     *
     * @param BackendInterface $backend
     * @return $this
     */
    public function setBackend(BackendInterface $backend);

    /**
     * Flush all changes in objects into storage
     *
     * @return $this
     */
    public function flush();
}
