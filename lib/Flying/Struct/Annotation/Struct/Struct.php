<?php

namespace Flying\Struct\Annotation\Struct;

use Doctrine\Common\Annotations\Annotation\Attribute;
use Doctrine\Common\Annotations\Annotation\Attributes;
use Doctrine\Common\Annotations\Annotation\Target;
use Doctrine\Common\Annotations\AnnotationException;

/**
 * @Annotation
 * @Target({"CLASS", "ANNOTATION"})
 * @Attributes({
 *      @Attribute("name", required=true, type="string"),
 *      @Attribute("class", required=false, type="string"),
 * })
 */
class Struct extends Annotation
{
    /**
     * Class name of structure property
     *
     * @var string
     */
    private $class;
    /**
     * Inline structure properties
     *
     * @var array
     */
    private $properties = array();
    /**
     * Property configuration
     *
     * @var array
     */
    private $config = array();

    /**
     * {@inheritdoc}
     */
    protected function parseValues(array &$values)
    {
        parent::parseValues($values);
        if (array_key_exists('class', $values)) {
            $this->class = $values['class'];
            unset($values['class']);
        }
        if ((array_key_exists('value', $values)) && (is_array($values['value']))) {
            $this->properties = $values['value'];
            unset($values['value']);
        }
        $this->config = $values;
        foreach ($this->properties as $p) {
            if (!$p instanceof Annotation) {
                throw new AnnotationException('Inline structure property definition should be valid annotation');
            }
        }
        // Check if we got required properties
        if (((!is_string($this->class)) || ($this->class === '')) && (!count($this->properties))) {
            // We should have either explicitly defined structure properties or structure class name
            throw new AnnotationException('Required property annotation is missed: class');
        }
    }

    /**
     * Get structure class
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Get inline structure properties definition
     *
     * @return array
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * Get structure config
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }
}
