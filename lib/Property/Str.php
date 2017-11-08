<?php

namespace Flying\Struct\Property;

/**
 * Structure property with string value
 */
class Str extends Property
{
    /**
     * {@inheritdoc}
     */
    public function validateConfig($name, &$value)
    {
        /** @noinspection DegradedSwitchInspection */
        switch ($name) {
            case 'maxlength':
                if ($value !== null) {
                    $value = max((int)$value, 1);
                }
                break;
            default:
                return parent::validateConfig($name, $value);
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function initConfig()
    {
        parent::initConfig();
        $this->mergeConfig([
            'maxlength' => null, // String length limitation
        ]);
    }

    /**
     * {@inheritdoc}
     * @throws \RuntimeException
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
            if (is_array($value)) {
                return false;
            }

            if (is_object($value)) {
                if (method_exists($value, 'toString')) {
                    $value = $value->toString();
                } elseif (method_exists($value, '__toString')) {
                    $value = (string)$value;
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
}
