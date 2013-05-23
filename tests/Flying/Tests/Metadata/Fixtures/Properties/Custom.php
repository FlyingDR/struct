<?php

namespace Flying\Tests\Metadata\Fixtures\Properties;

use Flying\Struct\Property\AbstractProperty;

/**
 * Custom structure property
 */
class Custom extends AbstractProperty
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
