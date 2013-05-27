<?php

namespace Flying\Struct;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\Cache;
use Flying\Struct\Configuration\NamespacesMap;
use Flying\Struct\Metadata\AnnotationParser;
use Flying\Struct\Metadata\MetadataManager;
use Flying\Struct\Metadata\MetadataManagerInterface;
use Flying\Struct\Metadata\MetadataParserInterface;

/**
 * Structures configuration
 */
class Configuration
{
    /**
     * Cache for structures information
     * @var Cache
     */
    protected $_cache;
    /**
     * Namespaces map for structure classes
     * @var NamespacesMap
     */
    protected $_structNsMap;
    /**
     * Namespaces map for property classes
     * @var NamespacesMap
     */
    protected $_propertyNsMap;
    /**
     * Namespaces map for annotation classes
     * @var NamespacesMap
     */
    protected $_annotationNsMap;
    /**
     * Structures metadata manager
     * @var MetadataManagerInterface
     */
    protected $_metadataManager;
    /**
     * Structures metadata parser
     * @var MetadataParserInterface
     */
    protected $_metadataParser;

    /**
     * Get cache for structures information
     *
     * @return Cache
     */
    public function getCache()
    {
        if (!$this->_cache) {
            $this->_cache = new ArrayCache();
        }
        return $this->_cache;
    }

    /**
     * Set cache for structures information
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
     * Get namespaces map for structure classes
     *
     * @return NamespacesMap
     */
    public function getStructNamespacesMap()
    {
        if (!$this->_structNsMap) {
            $this->_structNsMap = new NamespacesMap();
            $this->_structNsMap->add('default', 'Flying\Struct');
        }
        return $this->_structNsMap;
    }

    /**
     * Get namespaces map for property classes
     *
     * @return NamespacesMap
     */
    public function getPropertyNamespacesMap()
    {
        if (!$this->_propertyNsMap) {
            $this->_propertyNsMap = new NamespacesMap();
            $this->_propertyNsMap->add('default', 'Flying\Struct\Property');
        }
        return $this->_propertyNsMap;
    }

    /**
     * Get namespaces map for annotation classes
     *
     * @return NamespacesMap
     */
    public function getAnnotationNamespacesMap()
    {
        if (!$this->_annotationNsMap) {
            $this->_annotationNsMap = new NamespacesMap();
            $this->_annotationNsMap->add('default', 'Flying\Struct\Annotation');
        }
        return $this->_annotationNsMap;
    }

    /**
     * Get structures metadata manager
     *
     * @return MetadataManagerInterface
     */
    public function getMetadataManager()
    {
        if (!$this->_metadataManager) {
            $this->_metadataManager = new MetadataManager();
        }
        return $this->_metadataManager;
    }

    /**
     * Set structures metadata manager
     *
     * @param MetadataManagerInterface $manager
     * @return $this
     */
    public function setMetadataManager(MetadataManagerInterface $manager)
    {
        $this->_metadataManager = $manager;
        return $this;
    }

    /**
     * Get structures metadata parser
     *
     * @return MetadataParserInterface
     */
    public function getMetadataParser()
    {
        if (!$this->_metadataParser) {
            $this->_metadataParser = new AnnotationParser();
        }
        return $this->_metadataParser;
    }

    /**
     * Set structures metadata parser
     *
     * @param MetadataParserInterface $parser
     * @return $this
     */
    public function setMetadataParser(MetadataParserInterface $parser)
    {
        $this->_metadataParser = $parser;
        return $this;
    }

}