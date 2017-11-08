<?php

namespace Flying\Struct\Common;

/**
 * Interface for defining complex properties for structure
 */
interface ComplexPropertyInterface extends \Countable, \ArrayAccess, SimplePropertyInterface
{
    /**
     * Get structure contents as associative array
     *
     * @return array
     */
    public function toArray();
}
