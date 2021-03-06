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
     *
     * @var array
     */
    private $elements = [];
    /**
     * Cached value of "allowed" configuration option
     *
     * @var array
     */
    private $allowed;

    /**
     * {@inheritdoc}
     * @return array
     */
    public function getValue()
    {
        return $this->elements;
    }

    /**
     * {@inheritdoc}
     * @throws \InvalidArgumentException
     */
    public function setValue($value)
    {
        if (!is_array($value)) {
            if (is_object($value) && method_exists($value, 'toArray')) {
                $value = $value->toArray();
            } else {
                throw new \InvalidArgumentException('Only array values are accepted for collections');
            }
        }
        $elements = [];
        foreach ((array)$value as $k => $v) {
            if ($this->normalize($v, $k)) {
                $elements[$k] = $v;
            }
        }
        if (count($value) && (!count($elements))) {
            // There is no valid elements into given value
            $this->onInvalidValue($value);
            return false;
        }

        $this->elements = $elements;
        $this->onChange();
        return true;
    }

    /**
     * Normalize given value to make it compatible with property requirements
     *
     * @param mixed $value      Given property value (passed by reference)
     * @param int|string $key   OPTIONAL Key for given value in a case if multiple values are given
     * @return boolean          TRUE if value can be accepted, FALSE otherwise
     */
    protected function normalize(&$value, $key = null)
    {
        if (!parent::normalize($value)) {
            return false;
        }
        $allowed = $this->allowed;
        if (is_callable($allowed) && (!$allowed($value))) {
            return false;
        }
        if (is_string($allowed) && ((!is_object($value)) || (!$value instanceof $allowed))) {
            return false;
        }
        if (is_array($allowed) && (!is_callable($allowed)) && (!in_array($value, $allowed, true))) {
            return false;
        }
        return true;
    }

    /**
     * Invalid value setting handler
     *
     * @param mixed $value    Invalid value given to property
     * @param int|string $key OPTIONAL Key of this value
     * @return void
     */
    protected function onInvalidValue($value, $key = null)
    {

    }

    /**
     * Toggle given element in collection.
     * Adds element in collection if it is missed, removes if it is available
     *
     * @param mixed $element The element to toggle.
     * @return void
     * @throws \RuntimeException
     */
    public function toggle($element)
    {
        if ($this->normalize($element)) {
            // contains() and other methods are not used here
            // to avoid performance penalty from multiple calls to normalize()
            if (in_array($element, $this->elements, true)) {
                // Copy from removeElement()
                $changed = false;
                do {
                    $key = array_search($element, $this->elements, true);
                    if ($key !== false) {
                        unset($this->elements[$key]);
                        $changed = true;
                    }
                } while ($key !== false);
                if ($changed) {
                    $this->onChange();
                }
            } else {
                // Copy from add()
                $this->elements[] = $element;
                $this->onChange();
            }
        } else {
            $this->onInvalidValue($element);
        }
    }

    /**
     * Removes the specified element from the collection, if it is found.
     *
     * @param mixed $element The element to remove.
     * @return boolean          TRUE if this collection contained the specified element, FALSE otherwise.
     * @throws \RuntimeException
     */
    public function removeElement($element)
    {
        $changed = false;
        if (!$this->normalize($element)) {
            $this->onInvalidValue($element);
            return $changed;
        }
        do {
            $key = array_search($element, $this->elements, true);
            if ($key !== false) {
                unset($this->elements[$key]);
                $changed = true;
            }
        } while ($key !== false);
        if ($changed) {
            $this->onChange();
        }
        return $changed;
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
        return in_array($element, $this->elements, true);
    }

    /**
     * Gets the index/key of a given element.
     *
     * @param mixed $element The element to search for.
     * @return int|string|boolean   The key/index of the element or FALSE if the element was not found.
     */
    public function indexOf($element)
    {
        if (!$this->normalize($element)) {
            return false;
        }
        return array_search($element, $this->elements, true);
    }

    /**
     * Gets all keys/indices of the collection.
     *
     * @return array
     */
    public function getKeys()
    {
        return array_keys($this->elements);
    }

    /**
     * Gets all values of the collection.
     *
     * @return array
     */
    public function getValues()
    {
        return array_values($this->elements);
    }

    /**
     * Checks whether the collection is empty.
     *
     * @return boolean
     */
    public function isEmpty()
    {
        return !$this->elements;
    }

    /**
     * Clears the collection, removing all elements.
     *
     * @return void
     * @throws \RuntimeException
     */
    public function clear()
    {
        $this->elements = [];
        $this->onChange();
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->elements);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->elements);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetExists($offset)
    {
        return $this->containsKey($offset);
    }

    /**
     * Checks whether the collection contains an element with the specified key/index.
     *
     * @param string|integer $key The key/index to check for.
     * @return boolean
     */
    public function containsKey($key)
    {
        return array_key_exists($key, $this->elements) || isset($this->elements[$key]);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Gets the element at the specified key/index.
     *
     * @param string|integer $key The key/index of the element to retrieve.
     * @return mixed
     */
    public function get($key)
    {
        if (array_key_exists($key, $this->elements)) {
            return $this->elements[$key];
        }
        return null;
    }

    /**
     * {@inheritDoc}
     * @throws \RuntimeException
     */
    public function offsetSet($offset, $value)
    {
        if ($offset !== null) {
            $this->set($offset, $value);
        } else {
            $this->add($value);
        }
    }

    /**
     * Sets an element in the collection at the specified key/index.
     *
     * @param string|integer $key The key/index of the element to set.
     * @param mixed $element      The element to set.
     * @return void
     * @throws \RuntimeException
     */
    public function set($key, $element)
    {
        if ($this->normalize($element)) {
            $this->elements[$key] = $element;
            $this->onChange();
        } else {
            $this->onInvalidValue($element, $key);
        }
    }

    /**
     * Adds an element at the end of the collection.
     *
     * @param mixed $element The element to add.
     * @return void
     * @throws \RuntimeException
     */
    public function add($element)
    {
        if ($this->normalize($element)) {
            $this->elements[] = $element;
            $this->onChange();
        } else {
            $this->onInvalidValue($element);
        }
    }

    /**
     * {@inheritDoc}
     * @throws \RuntimeException
     */
    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }

    /**
     * Removes the element at the specified index from the collection.
     *
     * @param string|integer $key The kex/index of the element to remove.
     * @return mixed                The removed element or NULL, if the collection did not contain the element.
     * @throws \RuntimeException
     */
    public function remove($key)
    {
        if (array_key_exists($key, $this->elements) || isset($this->elements[$key])) {
            $removed = $this->elements[$key];
            unset($this->elements[$key]);
            $this->onChange();
            return $removed;
        }
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return $this->elements;
    }

    /**
     * {@inheritdoc}
     * @throws \InvalidArgumentException
     */
    public function validateConfig($name, &$value)
    {
        switch ($name) {
            case 'default':
                if (!is_array($value)) {
                    if (is_object($value) && method_exists($value, 'toArray')) {
                        $value = $value->toArray();
                    } else {
                        throw new \InvalidArgumentException('Only arrays are accepted as default values for collection properties');
                    }
                }
                break;
            case 'allowed':
                $valid = false;
                if (($value === null) || is_callable($value)) {
                    // Explicitly defined validator or empty validator
                    $valid = true;
                } elseif (is_array($value) && (count($value) === 1) && isset($value[0]) && is_string($value[0])) {
                    // This is probably validator defined through annotation's "allowed" parameter
                    $v = $value[0];
                    if (class_exists($v)) {
                        // Class name for validation
                        $value = $v;
                        $valid = true;
                    } elseif (method_exists($this, $v)) {
                        // Name of validation method
                        $value = [$this, $v];
                        $valid = true;
                    } else {
                        // Explicitly given list of valid values
                        $valid = true;
                    }
                } elseif (is_string($value)) {
                    if (class_exists($value)) {
                        // Explicitly given class name for validation
                        $valid = true;
                    } elseif (method_exists($this, $value)) {
                        // Explicitly given name of validation method
                        $value = [$this, $value];
                        $valid = true;
                    }
                } /** @noinspection NotOptimalIfConditionsInspection */ elseif (is_array($value)) {
                    // Explicitly given list of valid values
                    $valid = true;
                }
                if (!$valid) {
                    throw new \InvalidArgumentException('Unable to recognize given validator for collection');
                }
                break;
            default:
                return parent::validateConfig($name, $value);
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function reset()
    {
        // No change notification should be made for reset,
        // property value should be set to its default
        $flag = $this->skipNotify;
        $this->skipNotify = true;
        /** @var array $default */
        $default = (array)$this->getConfig('default');
        foreach ($default as $k => &$v) {
            if (!$this->normalize($v, $k)) {
                throw new Exception('Default value for property class ' . get_class($this) . ' is not acceptable for property validation rules');
            }
        }
        unset($v);
        $this->elements = $default;
        $this->skipNotify = $flag;
    }

    /**
     * {@inheritdoc}
     */
    protected function initConfig()
    {
        parent::initConfig();
        $this->mergeConfig([
            'default' => [], // Default value for collection
            'allowed' => null, // Either list of allowed values for collection elements
            // or callable to test if element is allowed to be in collection
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function onConfigChange($name, $value)
    {
        /** @noinspection DegradedSwitchInspection */
        switch ($name) {
            case 'allowed':
                $this->allowed = $value;
                break;
            default:
                parent::onConfigChange($name, $value);
                break;
        }
    }
}
