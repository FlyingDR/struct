<?php

namespace Flying\Struct\Metadata;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Annotations\SimpleAnnotationReader;
use Flying\Struct\Annotation\Struct\Annotation;
use Flying\Struct\Annotation\Struct\Property;
use Flying\Struct\Annotation\Struct\Struct;
use Flying\Struct\ConfigurationManager;
use Flying\Struct\Exception;

/**
 * Structures metadata parser implementation for parsing structure annotations
 */
class AnnotationParser extends AbstractMetadataParser
{
    /**
     * Annotations reader
     *
     * @var Reader
     */
    private $reader;
    /**
     * Namespaces for annotations autoloading
     *
     * @var array
     */
    private $namespaces = [];

    /**
     * Get annotations reader
     *
     * @return Reader
     */
    public function getReader()
    {
        if (!$this->reader) {
            $this->reader = new SimpleAnnotationReader();
            $namespaces = ConfigurationManager::getConfiguration()->getAnnotationNamespacesMap()->getAll();
            foreach ($namespaces as $ns) {
                $this->reader->addNamespace($ns);
            }
            $this->namespaces = array_reverse($namespaces, true);
            AnnotationRegistry::registerLoader([$this, 'loadClass']);
        }
        return $this->reader;
    }

    /**
     * Set annotations reader to use for parsing structure annotations
     *
     * @param Reader $reader
     *
     * @return $this
     */
    public function setReader(Reader $reader)
    {
        $this->reader = $reader;
        return $this;
    }

    /**
     * Load annotation class
     *
     * @param string $class
     *
     * @return boolean
     */
    public function loadClass($class)
    {
        if (class_exists($class, true)) {
            return true;
        }
        $class = ucfirst(trim($class, '\\'));
        foreach ($this->namespaces as $ns) {
            $fqcn = $ns . '\\' . $class;
            if (class_exists($fqcn, true)) {
                return true;
            }
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    protected function parseMetadata(\ReflectionClass $reflection, StructMetadata $metadata)
    {
        $reader = $this->getReader();
        $annotations = $reader->getClassAnnotations($reflection);
        foreach ($annotations as $annotation) {
            $metadata->addProperty($this->convertToMetadata($annotation));
        }
    }

    /**
     * Convert given structure annotation into structure metadata
     *
     * @param Annotation $annotation Structure annotation to convert
     *
     * @return PropertyMetadata
     * @throws Exception
     */
    protected function convertToMetadata(Annotation $annotation)
    {
        if ($annotation instanceof Property) {
            $class = $this->resolvePropertyClass($annotation->getType());
            if (!$class) {
                throw new Exception('Unable to resolve structure property class for type: ' . $annotation->getType());
            }
            $property = new PropertyMetadata($annotation->getName(), $class, $annotation->getConfig());
            return $property;
        } elseif ($annotation instanceof Struct) {
            if ($annotation->getClass()) {
                $class = $this->resolveStructClass($annotation->getClass());
                if (!$class) {
                    throw new Exception('Unable to resolve structure class: ' . $annotation->getClass());
                }
                $struct = ConfigurationManager::getConfiguration()->getMetadataManager()->getMetadata($class);
                if (!$struct instanceof StructMetadata) {
                    throw new Exception('Failed to get structure metadata for class: ' . $class);
                }
                $struct->setName($annotation->getName());
                $struct->setConfig($annotation->getConfig());
            } else {
                if (!count($annotation->getProperties())) {
                    throw new Exception('Structure metadata should have either class name or explicitly defined list of structure properties');
                }
                $struct = new StructMetadata($annotation->getName(), null, $annotation->getConfig());
                foreach ($annotation->getProperties() as $p) {
                    $struct->addProperty($this->convertToMetadata($p));
                }
            }
            return $struct;
        } else {
            throw new Exception('Unknown structure annotation type');
        }
    }
}
