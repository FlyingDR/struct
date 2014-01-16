<?php

namespace Flying\Struct\Storage;

/**
 * Structures storage backend using plain array
 */
class ArrayBackend implements BackendInterface
{
    /**
     * Storage contents
     *
     * @var array
     */
    protected $storage = array();

    /**
     * Load information by given key from storage
     *
     * @param string $key
     * @return mixed
     */
    public function load($key)
    {
        if (array_key_exists($key, $this->storage)) {
            return $this->storage[$key];
        }
        return null;
    }

    /**
     * Save given contents into storage
     *
     * @param string $key
     * @param mixed $contents
     * @return void
     */
    public function save($key, $contents)
    {
        $this->storage[$key] = $contents;
    }

    /**
     * Check if storage has an entry with given key
     *
     * @param string $key
     * @return boolean
     */
    public function has($key)
    {
        return array_key_exists($key, $this->storage);
    }

    /**
     * Remove storage entry with given key
     *
     * @param string $key
     * @return void
     */
    public function remove($key)
    {
        unset($this->storage[$key]);
    }

    /**
     * Clear storage contents
     *
     * @return void
     */
    public function clear()
    {
        $this->storage = array();
    }
}
