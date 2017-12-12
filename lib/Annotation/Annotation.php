<?php

namespace Flying\Struct\Annotation;

use Doctrine\Common\Annotations\AnnotationException;

/**
 * Base class for structure annotations
 */
abstract class Annotation
{
    /**
     * Name of structure property
     *
     * @var string
     */
    private $name;

    /**
     * Class constructor
     *
     * @param array $values
     * @throws AnnotationException
     */
    public function __construct(array $values)
    {
        $this->parseValues($values);
    }

    /**
     * Parse given annotation values
     *
     * @param array $values
     * @throws AnnotationException
     * @return void
     */
    protected function parseValues(array &$values)
    {
        if (array_key_exists('name', $values)) {
            $this->name = $values['name'];
            unset($values['name']);
        }
        // Check if we got required properties
        if ((!is_string($this->name)) || ($this->name === '')) {
            throw new AnnotationException('Required property annotation is missed: name');
        }
    }

    /**
     * Get structure property name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
