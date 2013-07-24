<?php

namespace Flying\Struct\Annotation\Struct;

use Doctrine\Common\Annotations\AnnotationException;

/**
 * @Annotation
 * @Target({"CLASS"})
 * @Attributes({
 * @Attribute("name", required=true, type="string"),
 * @Attribute("type", required=false, type="string"),
 * @Attribute("default", required=false, type="mixed"),
 * @Attribute("nullable", required=false, type="boolean")
 * })
 */
class Property
{
    /**
     * Property name
     * @var string
     */
    protected $_name;
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
     * Class constructor
     *
     * @param array $values
     * @throws AnnotationException
     * @return Property
     */
    public function __construct(array $values)
    {
        foreach ($values as $k => $v) {
            switch ($k) {
                case 'name':
                    $this->_name = $v;
                    break;
                case 'type':
                    $this->_type = $v;
                    break;
                default:
                    $this->_config[$k] = $v;
                    break;
            }
        }
        $type = $this->getDefaultType();
        if ($type !== null) {
            $this->_type = $type;
        }
        // Check if we got required properties
        if ((!is_string($this->_name)) || (!strlen($this->_name))) {
            throw new AnnotationException('Required property annotation is missed: name');
        }
        if ((!is_string($this->_type)) || (!strlen($this->_type))) {
            throw new AnnotationException('Required property annotation is missed: type');
        }
    }

    /**
     * Get property name
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
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
        return null;
    }

}
