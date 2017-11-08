<?php

namespace Flying\Struct\Property;

/**
 * Structure property with DateTime value
 */
class DateTime extends Property
{
    /**
     * {@inheritdoc}
     * @throws \RuntimeException
     */
    public function validateConfig($name, &$value)
    {
        switch ($name) {
            case 'format':
                if ((!is_string($value)) || ($value === '')) {
                    $value = null;
                }
                break;
            case 'default':
                if ($value === null) {
                    if (!$this->getConfig('nullable')) {
                        $value = new \DateTime();
                    }
                } elseif (is_string($value)) {
                    $value = new \DateTime($value);
                } elseif (!($value instanceof \DateTime)) {
                    return false;
                }
                break;
            default:
                return parent::validateConfig($name, $value);
        }
        return true;
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

        if ($value instanceof \DateTime) {
            return true;
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

        if (!is_string($value)) {
            return false;
        }

        $format = $this->getConfig('format');
        if ($format) {
            $value = \DateTime::createFromFormat($format, $value);
        } else {
            $value = new \DateTime($value);
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
            'format' => null, // Default format of date/time information to use for constructing DateTime object from string
        ]);
    }
}
