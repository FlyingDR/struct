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
 *      @Attribute("type", required=false, type="string"),
 *      @Attribute("default", required=false, type="mixed"),
 *      @Attribute("nullable", required=false, type="boolean")
 * })
 */
class Property extends Annotation
{
    /**
     * Property type
     *
     * @var string
     */
    private $type;
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
        $this->type = $this->getDefaultType();
        if (array_key_exists('type', $values)) {
            $this->type = $values['type'];
            unset($values['type']);
        }
        $this->config = $values;
        // Check if we got required properties
        if ((!is_string($this->type)) || ($this->type === '')) {
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
        return $this->type;
    }

    /**
     * Get property config
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Get default property type
     *
     * @return string|null
     */
    protected function getDefaultType()
    {
        $type = explode('\\', get_class($this));
        $type = array_pop($type);
        $type = lcfirst($type);
        return ($type !== 'property') ? $type : null;
    }
}
