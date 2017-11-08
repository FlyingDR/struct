<?php

namespace Flying\Tests\Property\Fixtures;

use Flying\Struct\Property\Collection as BaseCollection;

/**
 * Collection object to test functionality
 * of custom validator through class method call
 */
class CollectionWithCustomValidator extends BaseCollection
{
    /**
     * Custom validator
     *
     * @param mixed $value
     * @return boolean
     */
    protected function validate($value)
    {
        return (is_int($value) && ($value % 5 === 0));
    }
}
