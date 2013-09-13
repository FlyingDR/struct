<?php

namespace Flying\Struct\Storage;

/**
 * Interface for implementing structures storage backend
 */
interface BackendInterface
{
    /**
     * Load information by given key from storage
     *
     * @param string $key
     * @return mixed
     */
    public function load($key);

    /**
     * Save given contents into storage
     *
     * @param string $key
     * @param mixed $contents
     * @return void
     */
    public function save($key, $contents);

    /**
     * Check if storage has an entry with given key
     *
     * @param string $key
     * @return boolean
     */
    public function has($key);

    /**
     * Remove storage entry with given key
     *
     * @param string $key
     * @return void
     */
    public function remove($key);

    /**
     * Clear storage contents
     *
     * @return void
     */
    public function clear();
}
