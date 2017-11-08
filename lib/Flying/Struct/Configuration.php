<?php

namespace Flying\Struct;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\Cache;
use Flying\Struct\Configuration\NamespacesMap;
use Flying\Struct\Metadata\AnnotationParser;
use Flying\Struct\Metadata\MetadataManager;
use Flying\Struct\Metadata\MetadataManagerInterface;
use Flying\Struct\Metadata\MetadataParserInterface;
use Flying\Struct\Storage\ArrayBackend;
use Flying\Struct\Storage\BackendInterface;
use Flying\Struct\Storage\Storage;
use Flying\Struct\Storage\StorageInterface;

/**
 * Structures configuration
 */
class Configuration
{
    /**
     * Cache for structures information
     *
     * @var Cache
     */
    private $cache;
    /**
     * Namespaces map for structure classes
     *
     * @var NamespacesMap
     */
    private $structNsMap;
    /**
     * Namespaces map for property classes
     *
     * @var NamespacesMap
     */
    private $propertyNsMap;
    /**
     * Namespaces map for annotation classes
     *
     * @var NamespacesMap
     */
    private $annotationNsMap;
    /**
     * Structures metadata manager
     *
     * @var MetadataManagerInterface
     */
    private $metadataManager;
    /**
     * Structures metadata parser
     *
     * @var MetadataParserInterface
     */
    private $metadataParser;
    /**
     * Structures storage manager
     *
     * @var StorageInterface
     */
    private $storage;
    /**
     * Backend for structures storage
     *
     * @var BackendInterface
     */
    private $storageBackend;

    /**
     * Get cache for structures information
     *
     * @return Cache
     */
    public function getCache()
    {
        if (!$this->cache) {
            $this->cache = new ArrayCache();
        }
        return $this->cache;
    }

    /**
     * Set cache for structures information
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
     * Get namespaces map for structure classes
     *
     * @return NamespacesMap
     * @throws \InvalidArgumentException
     */
    public function getStructNamespacesMap()
    {
        if (!$this->structNsMap) {
            $this->structNsMap = new NamespacesMap();
            $this->structNsMap->add('Flying\Struct', 'default');
        }
        return $this->structNsMap;
    }

    /**
     * Get namespaces map for property classes
     *
     * @return NamespacesMap
     * @throws \InvalidArgumentException
     */
    public function getPropertyNamespacesMap()
    {
        if (!$this->propertyNsMap) {
            $this->propertyNsMap = new NamespacesMap();
            $this->propertyNsMap->add('Flying\Struct\Property', 'default');
        }
        return $this->propertyNsMap;
    }

    /**
     * Get namespaces map for annotation classes
     *
     * @return NamespacesMap
     * @throws \InvalidArgumentException
     */
    public function getAnnotationNamespacesMap()
    {
        if (!$this->annotationNsMap) {
            $this->annotationNsMap = new NamespacesMap();
            $this->annotationNsMap->add('Flying\Struct\Annotation', 'default');
        }
        return $this->annotationNsMap;
    }

    /**
     * Get structures metadata manager
     *
     * @return MetadataManagerInterface
     */
    public function getMetadataManager()
    {
        if (!$this->metadataManager) {
            $this->metadataManager = new MetadataManager();
        }
        return $this->metadataManager;
    }

    /**
     * Set structures metadata manager
     *
     * @param MetadataManagerInterface $manager
     * @return $this
     */
    public function setMetadataManager(MetadataManagerInterface $manager)
    {
        $this->metadataManager = $manager;
        return $this;
    }

    /**
     * Get structures metadata parser
     *
     * @return MetadataParserInterface
     */
    public function getMetadataParser()
    {
        if (!$this->metadataParser) {
            $this->metadataParser = new AnnotationParser();
        }
        return $this->metadataParser;
    }

    /**
     * Set structures metadata parser
     *
     * @param MetadataParserInterface $parser
     * @return $this
     */
    public function setMetadataParser(MetadataParserInterface $parser)
    {
        $this->metadataParser = $parser;
        return $this;
    }

    /**
     * Get storage container
     *
     * @return StorageInterface
     */
    public function getStorage()
    {
        if (!$this->storage) {
            $this->storage = new Storage();
        }
        return $this->storage;
    }

    /**
     * Set storage container
     *
     * @param StorageInterface $storage
     * @return $this
     */
    public function setStorage(StorageInterface $storage)
    {
        $this->storage = $storage;
        return $this;
    }

    /**
     * Get backend for structures storage
     *
     * @return BackendInterface
     */
    public function getStorageBackend()
    {
        if (!$this->storageBackend) {
            $this->storageBackend = new ArrayBackend();
        }
        return $this->storageBackend;
    }

    /**
     * Set backend for structures storage
     *
     * @param BackendInterface $backend
     * @return $this
     */
    public function setStorageBackend(BackendInterface $backend)
    {
        $this->storageBackend = $backend;
        return $this;
    }
}
