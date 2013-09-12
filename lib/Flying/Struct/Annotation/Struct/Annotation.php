<?php

namespace Flying\Struct\Annotation\Struct;

use Doctrine\Common\Annotations\AnnotationException;

/**
 * Base class for structure annotations
 */
abstract class Annotation
{
    /**
     * Name of structure property
     * @var string
     */
    protected $_name;

    /**
     * Class constructor
     *
     * @param array $values
     * @return Annotation
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
            $this->_name = $values['name'];
            unset($values['name']);
        }
        // Check if we got required properties
        if ((!is_string($this->_name)) || (!strlen($this->_name))) {
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
        return $this->_name;
    }

}
