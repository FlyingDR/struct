<?php

namespace Flying\Struct\Property;

/**
 * Structure property with string value
 */
class String extends AbstractProperty
{

    /**
     * {@inheritdoc}
     */
    protected function getConfigOptions()
    {
        return (array_merge(parent::getConfigOptions(), array(
            'maxlength' => null, // String length limitation
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
            if (is_array($value)) {
                return false;
            } elseif (is_object($value)) {
                if (method_exists($value, 'toString')) {
                    $value = $value->toString();
                } elseif (method_exists($value, '__toString')) {
                    $value = $value->__toString();
                } else {
                    return false;
                }
            }
        }
        $value = (string)$value;
        $maxlength = $this->getConfig('maxlength');
        if (($maxlength !== null) && (strlen($value) > $maxlength)) {
            $value = substr($value, 0, $maxlength);
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function validateConfig($name, &$value)
    {
        switch ($name) {
            case 'maxlength':
                if ($value !== null) {
                    $value = max((int)$value, 1);
                }
                break;
            default:
                return parent::validateConfig($name, $value);
                break;
        }
        return true;
    }

}
