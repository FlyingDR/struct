<?php

namespace Flying\Struct\Property;

use Flying\Struct\Common\StructItemInterface;

/**
 * Interface for structure property
 */
interface PropertyInterface extends StructItemInterface, \Serializable
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

    /**
     * Reset property to its default state
     *
     * @return void
     */
    public function reset();

}
