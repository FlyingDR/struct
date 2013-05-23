<?php

namespace Flying\Struct\Metadata;

use Flying\Struct\ConfigurationManager;

/**
 * Base implementation of structures metadata parser
 */
abstract class AbstractMetadataParser implements MetadataParserInterface
{
    /**
     * Namespaces for property classes
     * @var array
     */
    protected $_nsProperty;
    /**
     * Namespaces for structure classes
     * @var array
     */
    protected $_nsStruct;

    /**
     * Resolve given property type into property FQCN
     *
     * @param string $class     Structure property class
     * @return string|null
     */
    protected function resolvePropertyClass($class)
    {
        if (!$this->_nsProperty) {
            $ns = ConfigurationManager::getConfiguration()->getPropertyNamespacesMap()->getAll();
            $this->_nsProperty = array_reverse($ns, true);
        }
        return ($this->resolveClass($class, $this->_nsProperty, 'Flying\Struct\Property\PropertyInterface'));
    }

    /**
     * Resolve given structure class into structure FQCN
     *
     * @param string $class     Structure class
     * @return string|null
     */
    protected function resolveStructClass($class)
    {
        if (!$this->_nsStruct) {
            $ns = ConfigurationManager::getConfiguration()->getStructNamespacesMap()->getAll();
            $this->_nsStruct = array_reverse($ns, true);
        }
        return ($this->resolveClass($class, $this->_nsStruct, 'Flying\Struct\StructInterface'));
    }

    /**
     * Resolve given class into FQCN class and check if it supports given namespace
     *
     * @param string $class         Class name to resolve
     * @param array $namespaces     List of namespace to use to expand given class name
     * @param string $interface     OPTIONAL Interface, class must implement
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
