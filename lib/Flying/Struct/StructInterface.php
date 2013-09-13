<?php

namespace Flying\Struct;

use Flying\Struct\Common\ComplexPropertyInterface;
use Flying\Struct\Common\UpdateNotifyListenerInterface;

/**
 * Interface for structures
 */
interface StructInterface extends \RecursiveIterator, ComplexPropertyInterface, UpdateNotifyListenerInterface
{
    /**
     * Retrieve value of structure property with given name and return $default if there is no such property
     *
     * @param string $name      Structure property name to get value of
     * @param mixed $default    OPTIONAL Default value to return in a case if property is not available
     * @return mixed
     */
    public function get($name, $default = null);

    /**
     * Set value of structure property with given name
     *
     * @param string|array $name    Either name of structure property to set value of
     *                              or array of structure properties to set
     * @param mixed $value          OPTIONAL New value for this property (only if $name is a string)
     * @return void
     */
    public function set($name, $value = null);
}
