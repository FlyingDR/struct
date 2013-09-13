<?php

namespace Flying\Struct\Property;

/**
 * Structure property with boolean value
 */
class Boolean extends Property
{
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
            $value = !empty($value);
        }
        $value = (boolean)$value;
        return true;
    }
}
