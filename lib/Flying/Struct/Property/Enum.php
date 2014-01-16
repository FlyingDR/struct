<?php

namespace Flying\Struct\Property;

/**
 * Structure property for enumerated set of values
 */
class Enum extends Property
{
    /**
     * Cached version of "values" configuration property
     *
     * @var array
     */
    protected $values = array();

    /**
     * {@inheritdoc}
     */
    protected function initConfig()
    {
        parent::initConfig();
        $this->mergeConfig(array(
            'values' => array(), // List of possible values in enum
        ));
    }

    /**
     * {@inheritdoc}
     */
    protected function normalize(&$value)
    {
        if (!parent::normalize($value)) {
            return false;
        }
        if (!in_array($value, $this->values, true)) {
            return false;
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function validateConfig($name, &$value)
    {
        switch ($name) {
            case 'values':
                if (!is_array($value)) {
                    if ((is_object($value)) && (method_exists($value, 'toArray'))) {
                        $value = $value->toArray();
                    } else {
                        throw new \InvalidArgumentException('Only arrays are accepted as list of values for enum property');
                    }
                }
                break;
            default:
                return parent::validateConfig($name, $value);
                break;
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function onConfigChange($name, $value, $merge)
    {
        switch ($name) {
            case 'values':
                $this->values = $value;
                break;
            default:
                parent::onConfigChange($name, $value, $merge);
                break;
        }
    }
}
