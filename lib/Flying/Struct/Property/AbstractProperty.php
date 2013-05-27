<?php

namespace Flying\Struct\Property;

use Flying\Config\ObjectConfig;
use Flying\Struct\Common\UpdateNotifyListenerInterface;
use Flying\Struct\Exception;

/**
 * Abstract implementation of structure property
 */
abstract class AbstractProperty implements PropertyInterface
{
    /**
     * Property value
     * @var mixed
     */
    protected $_value;
    /**
     * Property configuration
     * @var ObjectConfig
     */
    protected $_config;
    /**
     * TRUE to skip property change notification, FALSE otherwise
     * @var boolean
     */
    protected $_skipNotify = false;

    /**
     * Class constructor
     *
     * @param mixed $value      OPTIONAL Property value
     * @param array $config     OPTIONAL Configuration options for this property
     * @throws Exception
     * @return AbstractProperty
     */
    public function __construct($value = null, array $config = null)
    {
        // No change notification is required during object construction
        $flag = $this->_skipNotify;
        $this->_skipNotify = true;
        $this->initConfigObject($config);
        // We must be sure that property value is always valid
        // even if no value for the property is given explicitly
        if ($value !== null) {
            $this->set($value);
        } else {
            $this->reset();
        }
        $this->_skipNotify = $flag;
    }

    /**
     * Get property value
     *
     * @return mixed
     */
    public function get()
    {
        return ($this->_value);
    }

    /**
     * Set property value
     *
     * @param mixed $value
     * @return void
     */
    public function set($value)
    {
        if ($this->normalize($value)) {
            $this->_value = $value;
            $this->onChange();
        } else {
            $this->onInvalidValue($value);
        }
    }

    /**
     * Reset property to its default state
     *
     * @throws Exception
     * @return void
     */
    public function reset()
    {
        // No change notification should be made for reset,
        // property value should be set to its default
        $flag = $this->_skipNotify;
        $this->_skipNotify = true;
        $default = $this->getConfig('default');
        if ($this->normalize($default)) {
            $this->_value = $default;
        } else {
            throw new Exception('Default value for property class ' . get_class($this) . ' is not acceptable for property validation rules');
        }
        $this->_skipNotify = $flag;
    }

    /**
     * Value change notification handler
     *
     * @return void
     */
    protected function onChange()
    {
        if (!$this->_skipNotify) {
            $owner = $this->getConfig('update_notify_listener');
            if ($owner instanceof UpdateNotifyListenerInterface) {
                $owner->updateNotify($this);
            }
        }
    }

    /**
     * Invalid value setting handler
     *
     * @param mixed $value
     * @return void
     */
    protected function onInvalidValue($value)
    {

    }

    /**
     * Normalize given value to make it compatible with property requirements
     *
     * @param mixed $value  Given property value (passed by reference)
     * @return mixed        TRUE if value can be accepted, FALSE otherwise
     */
    abstract protected function normalize(&$value);

    /**
     * Get list of configuration options to use for config initialization
     *
     * @return array
     */
    protected function getConfigOptions()
    {
        return (array(
            'nullable'               => true, // TRUE if property value can be NULL, FALSE if not
            'default'                => null, // Default value for the property
            'update_notify_listener' => null, // Listener of property update notifications
        ));
    }

    /**
     * Check that given value of configuration option is valid
     *
     * @param string $name          Configuration option name
     * @param mixed $value          Option value (passed by reference)
     * @throws \InvalidArgumentException
     * @return boolean
     */
    public function validateConfig($name, &$value)
    {
        switch ($name) {
            case 'nullable':
                $value = (boolean)$value;
                break;
            case 'default':
                break;
            case 'update_notify_listener':
                if (($value !== null) && (!$value instanceof UpdateNotifyListenerInterface)) {
                    throw new \InvalidArgumentException('Update notifications listener must implement UpdateNotifyListenerInterface');
                }
                break;
        }
        return true;
    }

    protected function initConfigObject(array $config = null)
    {
        $this->_config = new ObjectConfig($this, $this->getConfigOptions(), array(
            'validateConfig' => array($this, 'validateConfig'),
        ), $config);
    }

    /**
     * Get object's configuration or configuration option with given name
     * If argument is passed as string - value of configuration option with this name will be returned
     * If argument is some kind of configuration options set - it will be merged with current object's configuration and returned
     * If no argument is passed - current object's configuration will be returned
     *
     * @param string|array|null $config     OPTIONAL Option name to get or configuration options
     *                                      to override default object's configuration.
     * @return mixed
     */
    public function getConfig($config = null)
    {
        if (!$this->_config) {
            $this->initConfigObject();
        }
        return ($this->_config->getConfig($config));
    }

    /**
     * Set configuration options for object
     *
     * @param array|string $config          Configuration options to set
     * @param mixed $value                  If first parameter is passed as string then it will be treated as
     *                                      configuration option name and $value as its value
     * @return void
     */
    protected function setConfig($config, $value = null)
    {
        if (!$this->_config) {
            $this->initConfigObject();
        }
        $this->_config->setConfig($config, $value);
    }

    /**
     * Implementation of Serializable interface
     *
     * @return string
     */
    public function serialize()
    {
        return (serialize($this->_value));
    }

    /**
     * Implementation of Serializable interface
     *
     * @param array $data   Serialized object data
     * @return void
     */
    public function unserialize($data)
    {
        $flag = $this->_skipNotify;
        $this->_skipNotify = true;
        $value = unserialize($data);
        $this->set($value);
        $this->_skipNotify = $flag;
    }

}
