<?php

namespace Flying\Struct\Common;

/**
 * Interface for defining simple properties for structure
 */
interface SimplePropertyInterface extends \Serializable
{

    /**
     * Reset property to its default state
     *
     * @return void
     */
    public function reset();

}
