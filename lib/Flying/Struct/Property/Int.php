<?php

namespace Flying\Struct\Property;

/**
 * Structure property with integer value
 */
class Int extends Property
{

    /**
     * {@inheritdoc}
     */
    protected function initConfig()
    {
        parent::initConfig();
        $this->mergeConfig(array(
            'min' => null, // Minimum allowed value
            'max' => null, // Maximum allowed value
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
        if ($value === null) {
            return true;
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
