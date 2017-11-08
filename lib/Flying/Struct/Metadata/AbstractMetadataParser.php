<?php

namespace Flying\Struct\Metadata;

use Flying\Struct\ConfigurationManager;
use Flying\Struct\Exception;
use Flying\Struct\Property\PropertyInterface;
use Flying\Struct\StructInterface;

/**
 * Base implementation of structures metadata parser
 */
abstract class AbstractMetadataParser implements MetadataParserInterface
{
    /**
     * Namespaces for property classes
     *
     * @var array
     */
    private $nsProperty;
    /**
     * Namespaces for structure classes
     *
     * @var array
     */
    private $nsStruct;
    /**
     * @var MetadataManagerInterface
     */
    private $metadataManager;

    /**
     * Get structure metadata information for given class
     *
     * @param string $class Structure class name to parse metadata from
     *
     * @throws Exception
     * @return StructMetadata
     * @throws \InvalidArgumentException
     */
    public function getMetadata($class)
    {
        try {
            $reflection = new \ReflectionClass($class);
            $parent = $reflection->getParentClass();
            $metadata = null;
            if (($parent instanceof \ReflectionClass) && $parent->implementsInterface(StructInterface::class)) {
                $metadata = $this->getMetadataManager()->getMetadata($parent->getName());
            }
            if (!$metadata instanceof StructMetadata) {
                $metadata = new StructMetadata();
            }
            $metadata->setClass($class);
            $this->parseMetadata($reflection, $metadata);
            $reflection = new \ReflectionClass($class);
            if ($reflection->implementsInterface(MetadataModificationInterface::class)) {
                /** @var $class MetadataModificationInterface */
                $class::modifyMetadata($metadata);
            }
        } catch (\ReflectionException $e) {
            throw new \InvalidArgumentException('Failed to obtain exception for metadata class "' . $class . '"');
        }
        return $metadata;
    }

    /**
     * @return MetadataManagerInterface
     */
    public function getMetadataManager()
    {
        if (!$this->metadataManager) {
            $this->metadataManager = ConfigurationManager::getConfiguration()->getMetadataManager();
        }
        return $this->metadataManager;
    }

    /**
     * @param MetadataManagerInterface $metadataManager
     */
    public function setMetadataManager(MetadataManagerInterface $metadataManager)
    {
        $this->metadataManager = $metadataManager;
    }

    /**
     * Actual implementation of structure metadata parsing
     *
     * @param \ReflectionClass $reflection
     * @param StructMetadata $metadata
     * @return void
     */
    abstract protected function parseMetadata(\ReflectionClass $reflection, StructMetadata $metadata);

    /**
     * Resolve given property type into property FQCN
     *
     * @param string $class Structure property class
     * @return string|null
     * @throws \InvalidArgumentException
     */
    protected function resolvePropertyClass($class)
    {
        if (!$this->nsProperty) {
            $ns = ConfigurationManager::getConfiguration()->getPropertyNamespacesMap()->getAll();
            $this->nsProperty = array_reverse($ns, true);
        }
        return $this->resolveClass($class, $this->nsProperty, PropertyInterface::class);
    }

    /**
     * Resolve given class into FQCN class and check if it supports given namespace
     *
     * @param string $class     Class name to resolve
     * @param array $namespaces List of namespace to use to expand given class name
     * @param string $interface OPTIONAL Interface, class must implement
     * @return string|null
     */
    protected function resolveClass($class, array $namespaces, $interface = null)
    {
        $class = ucfirst(trim($class, '\\'));
        if (class_exists($class) && (($interface === null) || in_array($interface, class_implements($class), true))) {
            return $class;
        }
        foreach ($namespaces as $ns) {
            $fqcn = $ns . '\\' . $class;
            if (class_exists($fqcn) && (($interface === null) || in_array($interface, class_implements($fqcn), true))) {
                return $fqcn;
            }
        }
        return null;
    }

    /**
     * Resolve given structure class into structure FQCN
     *
     * @param string $class Structure class
     * @return string|null
     * @throws \InvalidArgumentException
     */
    protected function resolveStructClass($class)
    {
        if (!$this->nsStruct) {
            $ns = ConfigurationManager::getConfiguration()->getStructNamespacesMap()->getAll();
            $this->nsStruct = array_reverse($ns, true);
        }
        return $this->resolveClass($class, $this->nsStruct, StructInterface::class);
    }
}
