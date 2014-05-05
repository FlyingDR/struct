<?php

namespace Flying\Struct\Metadata;

use Flying\Struct\ConfigurationManager;
use Flying\Struct\Exception;

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
    protected $nsProperty;
    /**
     * Namespaces for structure classes
     *
     * @var array
     */
    protected $nsStruct;

    /**
     * Get structure metadata information for given class
     *
     * @param string $class Structure class name to parse metadata from
     *
     * @throws Exception
     * @return StructMetadata
     */
    public function getMetadata($class)
    {
        $reflection = new \ReflectionClass($class);
        $parent = $reflection->getParentClass();
        if (($parent instanceof \ReflectionClass) && ($parent->implementsInterface('Flying\Struct\StructInterface'))) {
            $metadata = $this->getMetadata($parent->getName());
        } else {
            $metadata = new StructMetadata();
        }
        $metadata->setClass($class);
        $this->parseMetadata($reflection, $metadata);
        $reflection = new \ReflectionClass($class);
        if ($reflection->implementsInterface('Flying\Struct\Metadata\MetadataModificationInterface')) {
            /** @var $class MetadataModificationInterface */
            $class::modifyMetadata($metadata);
        }
        return $metadata;
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
     */
    protected function resolvePropertyClass($class)
    {
        if (!$this->nsProperty) {
            $ns = ConfigurationManager::getConfiguration()->getPropertyNamespacesMap()->getAll();
            $this->nsProperty = array_reverse($ns, true);
        }
        return ($this->resolveClass($class, $this->nsProperty, 'Flying\Struct\Property\PropertyInterface'));
    }

    /**
     * Resolve given structure class into structure FQCN
     *
     * @param string $class Structure class
     * @return string|null
     */
    protected function resolveStructClass($class)
    {
        if (!$this->nsStruct) {
            $ns = ConfigurationManager::getConfiguration()->getStructNamespacesMap()->getAll();
            $this->nsStruct = array_reverse($ns, true);
        }
        return ($this->resolveClass($class, $this->nsStruct, 'Flying\Struct\StructInterface'));
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
        if (class_exists($class, true)) {
            if (($interface === null) || (in_array($interface, class_implements($class)))) {
                return $class;
            }
        }
        foreach ($namespaces as $ns) {
            $fqcn = $ns . '\\' . $class;
            if (class_exists($fqcn, true)) {
                if (($interface === null) || (in_array($interface, class_implements($fqcn)))) {
                    return $fqcn;
                }
            }
        }
        return null;
    }
}
