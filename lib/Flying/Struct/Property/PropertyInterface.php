<?php

namespace Flying\Struct\Property;

use Flying\Struct\Common\SimplePropertyInterface;

/**
 * Interface for structure property
 */
interface PropertyInterface extends SimplePropertyInterface
{
    /**
     * Get property value
     *
     * @return mixed
     */
    public function getValue();

    /**
     * Set property value
     *
     * @param mixed $value
     * @return void
     */
    public function setValue($value);

}
