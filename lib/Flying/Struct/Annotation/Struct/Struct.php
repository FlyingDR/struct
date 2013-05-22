<?php

namespace Flying\Struct\Annotation\Struct;

use Doctrine\Common\Annotations\AnnotationException;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
class Struct
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
    protected $_class;
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
     * @return Struct
     */
    public function __construct(array $values)
    {
        foreach ($values as $k => $v) {
            switch ($k) {
                case 'name':
                    $this->_name = $v;
                    break;
                case 'class':
                    $this->_class = $v;
                    break;
                default:
                    $this->_config[$k] = $v;
            }
        }
        // Check if we got required properties
        if ((!is_string($this->_class)) || (!strlen($this->_class))) {
            throw new AnnotationException('Required structure annotation is missed: class');
        }
    }

    /**
     * Get structure name
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
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
