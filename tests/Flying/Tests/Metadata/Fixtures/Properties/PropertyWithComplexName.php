<?php

namespace Flying\Tests\Metadata\Fixtures\Properties;

use Flying\Struct\Property\AbstractProperty;

/**
 * Custom structure property to test handling of classes with complex names
 */
class PropertyWithComplexName extends AbstractProperty
{

    /**
     * {@inheritdoc}
     */
    protected function normalize(&$value)
    {
        if ($value === null) {
            return $this->getConfig('nullable');
        }
        return true;
    }

}
