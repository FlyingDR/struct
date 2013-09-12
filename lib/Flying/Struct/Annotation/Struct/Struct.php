<?php

namespace Flying\Struct\Annotation\Struct;

use Doctrine\Common\Annotations\AnnotationException;

/**
 * @Annotation
 * @Target({"CLASS"})
 * @Attributes({
 * @Attribute("name", required=true, type="string"),
 * @Attribute("class", required=true, type="string"),
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
        $this->_config = $values;
        // Check if we got required properties
        if ((!is_string($this->_class)) || (!strlen($this->_class))) {
            throw new AnnotationException('Required structure annotation is missed: class');
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
     * Get structure config
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->_config;
    }

}
