<?php

namespace Flying\Tests\Metadata\Fixtures\Stubs;

use Flying\Struct\StructInterface;

/**
 * Stub class for StructInterface
 */
class StructStub implements StructInterface
{

    /**
     * Retrieve value of structure property with given name and return $default if there is no such property
     *
     * @param string $name      Structure property name to get value of
     * @param mixed $default    OPTIONAL Default value to return in a case if property is not available
     * @return mixed
     */
    public function get($name, $default = null)
    {

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

    }

    /**
     * Defined by Countable interface
     *
     * @return int
     */
    public function count()
    {

    }

    /**
     * Defined by Iterator interface
     *
     * @return mixed
     */
    public function current()
    {

    }

    /**
     * Defined by Iterator interface
     *
     * @return mixed
     */
    public function key()
    {

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

    }

    /**
     * Defined by RecursiveIterator interface
     *
     * @return boolean
     */
    public function hasChildren()
    {

    }

    /**
     * Defined by RecursiveIterator interface
     *
     * @return \RecursiveIterator
     */
    public function getChildren()
    {

    }

    /**
     * Defined by ArrayAccess interface
     *
     * @param mixed $offset
     * @return boolean
     */
    public function offsetExists($offset)
    {

    }

    /**
     * Defined by ArrayAccess interface
     *
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {

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

    }

    /**
     * Implementation of Serializable interface
     *
     * @param array $data   Serialized object data
     * @return void
     */
    public function unserialize($data)
    {

    }

}
