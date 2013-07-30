<?php

namespace Flying\Struct\Metadata;

use Doctrine\Common\Cache\Cache;
use Flying\Struct\ConfigurationManager;
use Flying\Struct\StructInterface;

/**
 * Structures metadata manager
 */
class MetadataManager implements MetadataManagerInterface
{
    /**
     * Structures metadata parser
     * @var MetadataParserInterface
     */
    protected $_parser;
    /**
     * Structures metadata cache
     * @var Cache
     */
    protected $_cache;
    /**
     * Structures metadata
     * @var array
     */
    protected $_metadata = array();
    /**
     * Prefix for cache entries for structure metadata
     * @var string
     */
    protected $_cachePrefix = 'StructMetadata_';

    /**
     * Get metadata parser
     *
     * @return MetadataParserInterface
     */
    public function getParser()
    {
        if (!$this->_parser) {
            $this->_parser = ConfigurationManager::getConfiguration()->getMetadataParser();
        }
        return $this->_parser;
    }

    /**
     * Set metadata parser
     *
     * @param MetadataParserInterface $parser
     * @return $this
     */
    public function setParser(MetadataParserInterface $parser)
    {
        $this->_parser = $parser;
        return $this;
    }

    /**
     * Get metadata cache
     *
     * @return Cache
     */
    public function getCache()
    {
        if (!$this->_cache) {
            $this->_cache = ConfigurationManager::getConfiguration()->getCache();
        }
        return $this->_cache;
    }

    /**
     * Set metadata cache
     *
     * @param Cache $cache
     * @return $this
     */
    public function setCache(Cache $cache)
    {
        $this->_cache = $cache;
        return $this;
    }

    /**
     * Get cache key for given class
     *
     * @param string $class
     * @return string
     */
    protected function getCacheKey($class)
    {
        return $this->_cachePrefix . str_replace('\\', '_', $class);
    }

    /**
     * Get structure metadata information for given structure
     *
     * @param string|StructInterface $struct    Either structure class name or instance of structure object
     *                                          to get metadata for
     * @throws \InvalidArgumentException
     * @return StructMetadata|null
     */
    public function getMetadata($struct)
    {
        $class = null;
        // Get class name of given structure
        if (is_object($struct)) {
            if ($struct instanceof StructInterface) {
                $class = get_class($struct);
            } else {
                throw new \InvalidArgumentException('Structure class must implement StructInterface interface');
            }
        } elseif (is_string($struct)) {
            $reflection = new \ReflectionClass($struct);
            if (in_array('Flying\Struct\StructInterface', $reflection->getInterfaceNames())) {
                $class = $reflection->getName();
            } else {
                throw new \InvalidArgumentException('Structure class must implement StructInterface interface');
            }
        } else {
            throw new \InvalidArgumentException('Invalid structure information is given');
        }
        // Check local metadata storage - fastest possible way
        if (array_key_exists($class, $this->_metadata)) {
            return clone $this->_metadata[$class];
        }
        // Check metadata cache
        $cacheKey = $this->getCacheKey($class);
        if ($this->getCache()->contains($cacheKey)) {
            $metadata = $this->getCache()->fetch($cacheKey);
            if ($metadata instanceof StructMetadata) {
                $this->_metadata[$class] = $metadata;
                return clone $metadata;
            } else {
                // Cache has incorrect or corrupted entry
                $this->getCache()->delete($cacheKey);
            }
        }
        // Get metadata from parser
        $metadata = $this->getParser()->getMetadata($class);
        if ($metadata instanceof StructMetadata) {
            $this->_metadata[$class] = $metadata;
            $this->getCache()->save($cacheKey, $metadata);
            return clone $metadata;
        }
        // No metadata is found for structure
        return null;
    }

}
