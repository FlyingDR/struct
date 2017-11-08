<?php

namespace Flying\Tests\Property\Fixtures;

use Flying\Struct\Property\Property as BaseProperty;

/**
 * Test class to check property behavior in a case of unacceptable values passing
 */
class PropertyForUnacceptableValues extends BaseProperty
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
        return ($value === 'value');
    }
}
