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
     *
     * @var MetadataParserInterface
     */
    private $parser;
    /**
     * Structures metadata cache
     *
     * @var Cache
     */
    private $cache;
    /**
     * Structures metadata
     *
     * @var array
     */
    private $metadata = [];
    /**
     * Prefix for cache entries for structure metadata
     *
     * @var string
     */
    private $cachePrefix = 'StructMetadata_';

    /**
     * Get metadata parser
     *
     * @return MetadataParserInterface
     */
    public function getParser()
    {
        if (!$this->parser) {
            $this->parser = ConfigurationManager::getConfiguration()->getMetadataParser();
            if ($this->parser instanceof AbstractMetadataParser) {
                $this->parser->setMetadataManager($this);
            }
        }
        return $this->parser;
    }

    /**
     * Set metadata parser
     *
     * @param MetadataParserInterface $parser
     * @return $this
     */
    public function setParser(MetadataParserInterface $parser)
    {
        $this->parser = $parser;
        if ($this->parser instanceof AbstractMetadataParser) {
            $this->parser->setMetadataManager($this);
        }
        return $this;
    }

    /**
     * Get metadata cache
     *
     * @return Cache
     */
    public function getCache()
    {
        if (!$this->cache) {
            $this->cache = ConfigurationManager::getConfiguration()->getCache();
        }
        return $this->cache;
    }

    /**
     * Set metadata cache
     *
     * @param Cache $cache
     * @return $this
     */
    public function setCache(Cache $cache)
    {
        $this->cache = $cache;
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
        return $this->cachePrefix . str_replace('\\', '_', $class);
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
            if (in_array('Flying\Struct\StructInterface', $reflection->getInterfaceNames(), true)) {
                $class = $reflection->getName();
            } else {
                throw new \InvalidArgumentException('Structure class must implement StructInterface interface');
            }
        } else {
            throw new \InvalidArgumentException('Invalid structure information is given');
        }
        // Check local metadata storage - fastest possible way
        if (array_key_exists($class, $this->metadata)) {
            return clone $this->metadata[$class];
        }
        // Check metadata cache
        $cacheKey = $this->getCacheKey($class);
        if ($this->getCache()->contains($cacheKey)) {
            $metadata = $this->getCache()->fetch($cacheKey);
            if ($metadata instanceof StructMetadata) {
                $this->metadata[$class] = $metadata;
                return clone $metadata;
            } else {
                // Cache has incorrect or corrupted entry
                $this->getCache()->delete($cacheKey);
            }
        }
        // Get metadata from parser
        $metadata = $this->getParser()->getMetadata($class);
        if ($metadata instanceof StructMetadata) {
            $this->metadata[$class] = $metadata;
            $this->getCache()->save($cacheKey, $metadata);
            return clone $metadata;
        }
        // No metadata is found for structure
        return null;
    }
}
