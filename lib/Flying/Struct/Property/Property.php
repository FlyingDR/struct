<?php

namespace Flying\Struct\Property;

use Flying\Config\AbstractConfig;
use Flying\Struct\Common\UpdateNotifyListenerInterface;
use Flying\Struct\Exception;

/**
 * Abstract implementation of structure property
 */
class Property extends AbstractConfig implements PropertyInterface
{
    /**
     * Property value
     * @var mixed
     */
    protected $_value;
    /**
     * TRUE if we're in object constructor, FALSE otherwise
     * @var boolean
     */
    protected $_inConstructor = false;
    /**
     * TRUE to skip property change notification, FALSE otherwise
     * @var boolean
     */
    protected $_skipNotify = false;
    /**
     * Cached value of "nullable" configuration option
     * @var boolean
     */
    protected $_nullable = true;

    /**
     * Class constructor
     *
     * @param mixed $value      OPTIONAL Property value
     * @param array $config     OPTIONAL Configuration options for this property
     * @throws Exception
     * @return Property
     */
    public function __construct($value = null, array $config = null)
    {
        // No change notification is required during object construction
        $flag = $this->_skipNotify;
        $this->_skipNotify = true;
        $this->_inConstructor = true;
        $this->bootstrapConfig();
        $this->setConfig($config);
        // We must be sure that property value is always valid
        // even if no value for the property is given explicitly
        if ($value !== null) {
            if (!$this->setValue($value)) {
                $this->reset();
            }
        } else {
            $this->reset();
        }
        $this->_skipNotify = $flag;
        $this->_inConstructor = false;
    }

    /**
     * Get property value
     *
     * @return mixed
     */
    public function getValue()
    {
        return ($this->_value);
    }

    /**
     * Set property value
     *
     * @param mixed $value
     * @return boolean
     */
    public function setValue($value)
    {
        if ($this->normalize($value)) {
            $this->_value = $value;
            $this->onChange();
            return true;
        } else {
            $this->onInvalidValue($value);
            return false;
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
    protected function normalize(&$value)
    {
        if ((($value === null)) && (!$this->_nullable)) {
            return false;
        }
        if ($value instanceof PropertyInterface) {
            $value = $value->getValue();
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
            'nullable'               => true, // TRUE if property value can be NULL, FALSE if not
            'default'                => null, // Default value for the property
            'update_notify_listener' => null, // Listener of property update notifications
        ));
    }

    /**
     * {@inheritdoc}
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

    /**
     * {@inheritdoc}
     */
    protected function onConfigChange($name, $value, $merge)
    {
        // Configuration options are only defined during object construction
        // @TODO This should be changed after implementation of read-only configuration
        if (!$this->_inConstructor) {
            throw new \RuntimeException('Property configuration options can\t be changed in runtime');
        }
        switch ($name) {
            case 'nullable':
                $this->_nullable = $value;
                break;
        }
    }

    /**
     * Get property's configuration options prepared for serialization
     *
     * @return array
     */
    protected function getConfigForSerialization()
    {
        $config = $this->getConfig();
        unset($config['update_notify_listener']);
        return $config;
    }

    /**
     * Implementation of Serializable interface
     *
     * @return string
     */
    public function serialize()
    {
        return (serialize(array(
            'value'  => $this->getValue(),
            'config' => $this->getConfigForSerialization(),
        )));
    }

    /**
     * Implementation of Serializable interface
     *
     * @param array $data   Serialized object data
     * @throws \InvalidArgumentException
     * @return void
     */
    public function unserialize($data)
    {
        $data = @unserialize($data);
        if ((!is_array($data)) ||
            (!array_key_exists('value', $data)) ||
            (!array_key_exists('config', $data)) ||
            (!is_array($data['config']))
        ) {
            throw new \InvalidArgumentException('Serialized property information has invalid format');
        }
        $flag = $this->_skipNotify;
        $this->_skipNotify = true;
        $this->_inConstructor = true;
        $this->setConfig($data['config']);
        $this->setValue($data['value']);
        $this->_inConstructor = false;
        $this->_skipNotify = $flag;
    }

}
