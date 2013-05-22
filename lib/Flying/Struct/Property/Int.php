<?php

namespace Flying\Struct\Property;

/**
 * Structure property with string value
 */
class Int extends AbstractProperty
{

    /**
     * {@inheritdoc}
     */
    protected function getConfigOptions()
    {
        return (array_merge(parent::getConfigOptions(), array(
            'min' => null, // Minimum allowed value
            'max' => null, // Maximum allowed value
        )));
    }

    /**
     * {@inheritdoc}
     */
    protected function normalize(&$value)
    {
        if ($value === null) {
            return $this->getConfig('nullable');
        }
        if (!is_scalar($value)) {
            return false;
        }
        $value = (int)$value;
        $min = $this->getConfig('min');
        if ($min !== null) {
            $value = max($value, $min);
        }
        $max = $this->getConfig('max');
        if ($max !== null) {
            $value = min($value, $max);
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function validateConfig($name, &$value)
    {
        switch ($name) {
            case 'min':
            case 'max':
                if ($value !== null) {
                    $value = (int)$value;
                }
                break;
            default:
                return parent::validateConfig($name, $value);
                break;
        }
        return true;
    }

}
