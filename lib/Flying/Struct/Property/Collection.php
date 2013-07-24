<?php

namespace Flying\Struct\Property;

use Flying\Struct\Common\ComplexPropertyInterface;
use Flying\Struct\Exception;

/**
 * Basic implementation of collection of elements as structure property
 * Code of this class is partially taken from Doctrine\Common\Collections\Collection
 * with respect to authors of original code
 */
class Collection extends Property implements ComplexPropertyInterface, \IteratorAggregate
{
    /**
     * Collection elements
     * @var array
     */
    protected $_elements = array();
    /**
     * Cached value of "allowed" configuration option
     * @var array
     */
    protected $_allowed;

    /**
     * {@inheritdoc}
     * @return array
     */
    public function getValue()
    {
        return $this->_elements;
    }

    /**
     * {@inheritdoc}
     */
    public function setValue($value)
    {
        if (!is_array($value)) {
            if ((is_object($value)) && (method_exists($value, 'toArray'))) {
                $value = $value->toArray();
            } else {
                throw new \InvalidArgumentException('Only array values are accepted for collections');
            }
        }
        $elements = array();
        foreach ($value as $k => $v) {
            if ($this->normalize($v, $k)) {
                $elements[$k] = $v;
            }
        }
        if ((sizeof($value)) && (!sizeof($elements))) {
            // There is no valid elements into given value
            $this->onInvalidValue($value);
            return false;
        } else {
            $this->_elements = $elements;
            $this->onChange();
            return true;
        }
    }

    /**
     * Gets the element at the specified key/index.
     *
     * @param string|integer $key   The key/index of the element to retrieve.
     * @return mixed
     */
    public function get($key)
    {
        if (array_key_exists($key, $this->_elements)) {
            return $this->_elements[$key];
        }
        return null;
    }

    /**
     * Sets an element in the collection at the specified key/index.
     *
     * @param string|integer $key     The key/index of the element to set.
     * @param mixed $element          The element to set.
     * @return void
     */
    public function set($key, $element)
    {
        if ($this->normalize($element)) {
            $this->_elements[$key] = $element;
            $this->onChange();
        } else {
            $this->onInvalidValue($element, $key);
        }
    }

    /**
     * Adds an element at the end of the collection.
     *
     * @param mixed $element      The element to add.
     * @return void
     */
    public function add($element)
    {
        if ($this->normalize($element)) {
            $this->_elements[] = $element;
            $this->onChange();
        } else {
            $this->onInvalidValue($element);
        }
    }

    /**
     * Toggle given element in collection.
     * Adds element in collection if it is missed, removes if it is available
     *
     * @param mixed $element      The element to toggle.
     * @return void
     */
    public function toggle($element)
    {
        if ($this->normalize($element)) {
            // contains() and other methods are not used here
            // to avoid performance penalty from multiple calls to normalize()
            if (in_array($element, $this->_elements, true)) {
                // Copy from removeElement()
                $changed = false;
                do {
                    $key = array_search($element, $this->_elements, true);
                    if ($key !== false) {
                        unset($this->_elements[$key]);
                        $changed = true;
                    }
                } while ($key !== false);
                if ($changed) {
                    $this->onChange();
                }
            } else {
                // Copy from add()
                $this->_elements[] = $element;
                $this->onChange();
            }
        } else {
            $this->onInvalidValue($element);
        }
    }

    /**
     * Removes the element at the specified index from the collection.
     *
     * @param string|integer $key   The kex/index of the element to remove.
     * @return mixed                The removed element or NULL, if the collection did not contain the element.
     */
    public function remove($key)
    {
        if (isset($this->_elements[$key]) || array_key_exists($key, $this->_elements)) {
            $removed = $this->_elements[$key];
            unset($this->_elements[$key]);
            $this->onChange();
            return $removed;
        }
        return null;
    }

    /**
     * Removes the specified element from the collection, if it is found.
     *
     * @param mixed $element    The element to remove.
     * @return boolean          TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeElement($element)
    {
        $changed = false;
        if (!$this->normalize($element)) {
            $this->onInvalidValue($element);
            return $changed;
        }
        do {
            $key = array_search($element, $this->_elements, true);
            if ($key !== false) {
                unset($this->_elements[$key]);
                $changed = true;
            }
        } while ($key !== false);
        if ($changed) {
            $this->onChange();
        }
        return $changed;
    }

    /**
     * Checks whether the collection contains an element with the specified key/index.
     *
     * @param string|integer $key   The key/index to check for.
     * @return boolean
     */
    public function containsKey($key)
    {
        return isset($this->_elements[$key]) || array_key_exists($key, $this->_elements);
    }

    /**
     * Checks whether an element is contained in the collection.
     *
     * @param mixed $element The element to search for.
     * @return boolean
     */
    public function contains($element)
    {
        if (!$this->normalize($element)) {
            return false;
        }
        return in_array($element, $this->_elements, true);
    }

    /**
     * Gets the index/key of a given element.
     *
     * @param mixed $element        The element to search for.
     * @return int|string|boolean   The key/index of the element or FALSE if the element was not found.
     */
    public function indexOf($element)
    {
        if (!$this->normalize($element)) {
            return false;
        }
        return array_search($element, $this->_elements, true);
    }

    /**
     * Gets all keys/indices of the collection.
     *
     * @return array
     */
    public function getKeys()
    {
        return array_keys($this->_elements);
    }

    /**
     * Gets all values of the collection.
     *
     * @return array
     */
    public function getValues()
    {
        return array_values($this->_elements);
    }

    /**
     * Checks whether the collection is empty.
     *
     * @return boolean
     */
    public function isEmpty()
    {
        return !$this->_elements;
    }

    /**
     * Clears the collection, removing all elements.
     *
     * @return void
     */
    public function clear()
    {
        $this->_elements = array();
        $this->onChange();
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return sizeof($this->_elements);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->_elements);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetExists($offset)
    {
        return $this->containsKey($offset);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetSet($offset, $value)
    {
        if (isset($offset)) {
            $this->set($offset, $value);
        } else {
            $this->add($value);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return $this->_elements;
    }

    /**
     * Invalid value setting handler
     *
     * @param mixed $value      Invalid value given to property
     * @param int|string $key   OPTIONAL Key of this value
     * @return void
     */
    protected function onInvalidValue($value, $key = null)
    {

    }

    /**
     * Normalize given value to make it compatible with property requirements
     *
     * @param mixed $value      Given property value (passed by reference)
     * @param int|string $key   OPTIONAL Key for given value in a case if multiple values are given
     * @return mixed            TRUE if value can be accepted, FALSE otherwise
     */
    protected function normalize(&$value, $key = null)
    {
        if (!parent::normalize($value)) {
            return false;
        }
        $allowed = $this->_allowed;
        if ((is_array($allowed)) && (!in_array($value, $allowed, true))) {
            return false;
        } elseif ((is_callable($allowed)) && (!$allowed($value))) {
            return false;
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function initConfig()
    {
        parent::initConfig();
        $this->mergeConfig(array(
            'default' => array(), // Default value for collection
            'allowed' => null, // Either list of allowed values for collection elements
            // or callable to test if element is allowed to be in collection
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function validateConfig($name, &$value)
    {
        switch ($name) {
            case 'default':
                if (!is_array($value)) {
                    if ((is_object($value)) && (method_exists($value, 'toArray'))) {
                        $value = $value->toArray();
                    } else {
                        throw new \InvalidArgumentException('Only arrays are accepted as default values for collection properties');
                    }
                }
                break;
            case 'allowed':
                if (($value !== null) && (!is_array($value)) && (!is_callable($value))) {
                    throw new \InvalidArgumentException('List of allowed values for collection should be either array or callable');
                }
                break;
            default:
                return parent::validateConfig($name, $value);
                break;
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function onConfigChange($name, $value, $merge)
    {
        switch ($name) {
            case 'allowed':
                $this->_allowed = $value;
                break;
            default:
                parent::onConfigChange($name, $value, $merge);
                break;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function reset()
    {
        // No change notification should be made for reset,
        // property value should be set to its default
        $flag = $this->_skipNotify;
        $this->_skipNotify = true;
        $default = $this->getConfig('default');
        foreach ($default as $k => $v) {
            if (!$this->normalize($v, $k)) {
                throw new Exception('Default value for property class ' . get_class($this) . ' is not acceptable for property validation rules');
            }
            $default[$k] = $v;
        }
        $this->_elements = $default;
        $this->_skipNotify = $flag;
    }

}