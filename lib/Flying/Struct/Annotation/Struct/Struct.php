<?php

namespace Flying\Struct\Annotation\Struct;

use Doctrine\Common\Annotations\AnnotationException;

/**
 * @Annotation
 * @Target({"CLASS", "ANNOTATION"})
 * @Attributes({
 * @Attribute("name", required=true, type="string"),
 * @Attribute("class", required=false, type="string"),
 * })
 */
class Struct extends Annotation
{
    /**
     * Class name of structure property
     * @var string
     */
    protected $_class;
    /**
     * Inline structure properties
     * @var array
     */
    protected $_properties = array();
    /**
     * Property configuration
     * @var array
     */
    protected $_config = array();

    /**
     * {@inheritdoc}
     */
    protected function parseValues(array &$values)
    {
        parent::parseValues($values);
        if (array_key_exists('class', $values)) {
            $this->_class = $values['class'];
            unset($values['class']);
        }
        if ((array_key_exists('value', $values)) && (is_array($values['value']))) {
            $this->_properties = $values['value'];
            unset($values['value']);
        }
        $this->_config = $values;
        foreach ($this->_properties as $p) {
            if (!$p instanceof Annotation) {
                throw new AnnotationException('Inline structure property definition should be valid annotation');
            }
        }
        // Check if we got required properties
        if ((!is_string($this->_class)) || (!strlen($this->_class))) {
            if (!sizeof($this->_properties)) {
                // We should have either explicitly defined structure properties or structure class name
                throw new AnnotationException('Required property annotation is missed: class');
            }
        }
    }

    /**
     * Get structure class
     *
     * @return string
     */
    public function getClass()
    {
        return $this->_class;
    }

    /**
     * Get inline structure properties definition
     *
     * @return array
     */
    public function getProperties()
    {
        return $this->_properties;
    }

    /**
     * Get structure config
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->_config;
    }

}
