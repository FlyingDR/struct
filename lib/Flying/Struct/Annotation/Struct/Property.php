<?php

namespace Flying\Struct\Annotation\Struct;

use Doctrine\Common\Annotations\AnnotationException;

/**
 * @Annotation
 * @Target({"CLASS", "ANNOTATION"})
 * @Attributes({
 * @Attribute("name", required=true, type="string"),
 * @Attribute("type", required=false, type="string"),
 * @Attribute("default", required=false, type="mixed"),
 * @Attribute("nullable", required=false, type="boolean")
 * })
 */
class Property extends Annotation
{
    /**
     * Property type
     * @var string
     */
    protected $_type;
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
        $this->_type = $this->getDefaultType();
        if (array_key_exists('type', $values)) {
            $this->_type = $values['type'];
            unset($values['type']);
        }
        $this->_config = $values;
        // Check if we got required properties
        if ((!is_string($this->_type)) || (!strlen($this->_type))) {
            throw new AnnotationException('Required property annotation is missed: type');
        }
    }

    /**
     * Get property type
     *
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * Get property config
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->_config;
    }

    /**
     * Get default property type
     *
     * @return string|null
     */
    protected function getDefaultType()
    {
        $type = explode('\\', strtolower(get_class($this)));
        $type = array_pop($type);
        return ($type !== 'property') ? $type : null;
    }

}
