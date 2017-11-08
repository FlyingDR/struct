<?php

namespace Flying\Tests\Metadata\Fixtures\Stubs;

use Flying\Struct\Common\SimplePropertyInterface;
use Flying\Struct\StructInterface;

/**
 * Stub class for StructInterface
 */
class StructStub implements StructInterface
{
    /**
     * Retrieve value of structure property with given name and return $default if there is no such property
     *
     * @param string $name   Structure property name to get value of
     * @param mixed $default OPTIONAL Default value to return in a case if property is not available
     * @return mixed
     */
    public function get($name, $default = null)
    {
        return null;
    }

    /**
     * Set value of structure property with given name
     *
     * @param string|array $name    Either name of structure property to set value of
     *                              or array of structure properties to set
     * @param mixed $value          OPTIONAL New value for this property (only if $name is a string)
     * @return void
     */
    public function set($name, $value = null)
    {

    }

    /**
     * Reset structure to its initial state
     *
     * @return void
     */
    public function reset()
    {

    }

    /**
     * Get structure contents as associative array
     *
     * @return array
     */
    public function toArray()
    {
        return [];
    }

    /**
     * Defined by Countable interface
     *
     * @return int
     */
    public function count()
    {
        return 0;
    }

    /**
     * Defined by Iterator interface
     *
     * @return mixed
     */
    public function current()
    {
        return null;
    }

    /**
     * Defined by Iterator interface
     *
     * @return mixed
     */
    public function key()
    {
        return null;
    }

    /**
     * Defined by Iterator interface
     *
     * @return void
     */
    public function next()
    {

    }

    /**
     * Defined by Iterator interface
     *
     * @return void
     */
    public function rewind()
    {

    }

    /**
     * Defined by Iterator interface
     *
     * @return boolean
     */
    public function valid()
    {
        return false;
    }

    /**
     * Defined by RecursiveIterator interface
     *
     * @return boolean
     */
    public function hasChildren()
    {
        return false;
    }

    /**
     * Defined by RecursiveIterator interface
     *
     * @return \RecursiveIterator
     */
    public function getChildren()
    {
        return new \RecursiveArrayIterator();
    }

    /**
     * Defined by ArrayAccess interface
     *
     * @param mixed $offset
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return false;
    }

    /**
     * Defined by ArrayAccess interface
     *
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return null;
    }

    /**
     * Defined by ArrayAccess interface
     *
     * @param mixed $offset
     * @param mixed $value
     * @return void
     */
    public function offsetSet($offset, $value)
    {

    }

    /**
     * Defined by ArrayAccess interface
     *
     * @param mixed $offset
     * @return void
     */
    public function offsetUnset($offset)
    {

    }

    /**
     * Implementation of Serializable interface
     *
     * @return string
     */
    public function serialize()
    {
        return 'N;';
    }

    /**
     * Implementation of Serializable interface
     *
     * @param array $data Serialized object data
     * @return void
     */
    public function unserialize($data)
    {

    }

    /**
     * Handle notification about update of given property
     *
     * @param SimplePropertyInterface $property
     * @return void
     */
    public function updateNotify(SimplePropertyInterface $property)
    {

    }
}
