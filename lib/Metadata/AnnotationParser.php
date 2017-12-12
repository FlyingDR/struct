<?php

namespace Flying\Struct\Metadata;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\Reader;
use Flying\Struct\Annotation\Annotation;
use Flying\Struct\Annotation\Property;
use Flying\Struct\Annotation\Struct;
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
     * {@inheritdoc}
     * @throws \Flying\Struct\Exception
     * @throws \InvalidArgumentException
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
     * Get annotations reader
     *
     * @return Reader
     * @throws \InvalidArgumentException
     */
    public function getReader()
    {
        if (!$this->reader) {
            $this->reader = new AnnotationReader();
            AnnotationRegistry::registerLoader('class_exists');
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
     * Convert given structure annotation into structure metadata
     *
     * @param Annotation $annotation Structure annotation to convert
     *
     * @return PropertyMetadata
     * @throws \InvalidArgumentException
     * @throws Exception
     */
    protected function convertToMetadata(Annotation $annotation)
    {
        if ($annotation instanceof Property) {
            $class = $this->resolvePropertyClass($annotation->getType());
            if (!is_string($class)) {
                throw new Exception('Unable to resolve structure property class for type: ' . $annotation->getType());
            }
            $property = new PropertyMetadata($annotation->getName(), $class, $annotation->getConfig());
            return $property;
        }

        if (!($annotation instanceof Struct)) {
            throw new Exception('Unknown structure annotation type');
        }

        if ($annotation->getClass()) {
            $class = $this->resolveStructClass($annotation->getClass());
            if (!is_string($class)) {
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
    }
}
