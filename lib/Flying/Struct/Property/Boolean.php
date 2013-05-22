<?php

namespace Flying\Struct\Property;

/**
 * Structure property with boolean value
 */
class Boolean extends AbstractProperty
{

    /**
     * {@inheritdoc}
     */
    protected function normalize(&$value)
    {
        if ($value === null) {
            return $this->getConfig('nullable');
        }
        $value = (boolean)$value;
        return true;
    }

}
