<?php

namespace Flying\Struct\Storage;

use Doctrine\Common\Cache\Cache;
use Flying\Struct\ConfigurationManager;

/**
 * Implementation of structures storage backend using structures cache
 */
class CacheBackend implements BackendInterface
{
    /**
     * Cache to use as a storage
     * @var Cache
     */
    protected $cache;

    /**
     * Load information by given key from storage
     *
     * @param string $key
     * @return mixed
     */
    public function load($key)
    {
        $contents = $this->getCache()->fetch($key);
        if ($contents === false) {
            return null;
        }
        return $contents;
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
        $this->getCache()->save($key, $contents);
    }

    /**
     * Check if storage has an entry with given key
     *
     * @param string $key
     * @return boolean
     */
    public function has($key)
    {
        return $this->getCache()->contains($key);
    }

    /**
     * Remove storage entry with given key
     *
     * @param string $key
     * @return void
     */
    public function remove($key)
    {
        $this->getCache()->delete($key);
    }

    /**
     * Clear storage contents
     *
     * @return void
     */
    public function clear()
    {
        // Cache doesn't have such function
    }

    /**
     * Get cache to use as storage
     *
     * @return Cache
     */
    protected function getCache()
    {
        if (!$this->cache) {
            $this->cache = ConfigurationManager::getConfiguration()->getCache();
        }
        return $this->cache;
    }
}
