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
     * TRUE to skip property change notification, FALSE otherwise
     *
     * @var boolean
     */
    protected $skipNotify = false;
    /**
     * Property value
     *
     * @var mixed
     */
    private $value;
    /**
     * Cached value of "nullable" configuration option
     *
     * @var boolean
     */
    private $nullable = true;

    /**
     * Class constructor
     *
     * @param mixed $value  OPTIONAL Property value
     * @param array $config OPTIONAL Configuration options for this property
     * @throws \Flying\Struct\Exception
     * @throws \RuntimeException
     */
    public function __construct($value = null, array $config = null)
    {
        // No change notification is required during object construction
        $flag = $this->skipNotify;
        $this->skipNotify = true;
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
        $this->skipNotify = $flag;
    }

    /**
     * Reset property to its default state
     *
     * @throws Exception
     * @return void
     * @throws \RuntimeException
     */
    public function reset()
    {
        // No change notification should be made for reset,
        // property value should be set to its default
        $flag = $this->skipNotify;
        $this->skipNotify = true;
        $default = $this->getConfig('default');
        if ($this->normalize($default)) {
            $this->value = $default;
        } else {
            throw new Exception('Default value for property class ' . get_class($this) . ' is not acceptable for property validation rules');
        }
        $this->skipNotify = $flag;
    }

    /**
     * Normalize given value to make it compatible with property requirements
     *
     * @param mixed $value  Given property value (passed by reference)
     * @return boolean      TRUE if value can be accepted, FALSE otherwise
     */
    protected function normalize(&$value)
    {
        if (($value === null) && (!$this->nullable)) {
            return false;
        }
        if ($value instanceof PropertyInterface) {
            $value = $value->getValue();
        }
        return true;
    }

    /**
     * {@inheritdoc}
     * @throws \InvalidArgumentException
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
     * Implementation of Serializable interface
     *
     * @return string
     * @throws \RuntimeException
     */
    public function serialize()
    {
        return serialize([
            'value'  => $this->getValue(),
            'config' => $this->getConfigForSerialization(),
        ]);
    }

    /**
     * Get property value
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set property value
     *
     * @param mixed $value
     * @return boolean
     * @throws \RuntimeException
     */
    public function setValue($value)
    {
        if ($this->normalize($value)) {
            $this->value = $value;
            $this->onChange();
            return true;
        }

        $this->onInvalidValue($value);
        return false;
    }

    /**
     * Get property's configuration options prepared for serialization
     *
     * @return array
     * @throws \RuntimeException
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
     * @param string $serialized Serialized object data
     * @return void
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);
        if ((!is_array($data)) ||
            (!array_key_exists('value', $data)) ||
            (!array_key_exists('config', $data)) ||
            (!is_array($data['config']))
        ) {
            throw new \InvalidArgumentException('Serialized property information has invalid format');
        }
        $flag = $this->skipNotify;
        $this->skipNotify = true;
        $this->setConfig($data['config']);
        $this->setValue($data['value']);
        $this->skipNotify = $flag;
    }

    /**
     * Value change notification handler
     *
     * @return void
     * @throws \RuntimeException
     */
    protected function onChange()
    {
        if ($this->skipNotify) {
            return;
        }
        $owner = $this->getConfig('update_notify_listener');
        if ($owner instanceof UpdateNotifyListenerInterface) {
            $owner->updateNotify($this);
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
     * {@inheritdoc}
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    protected function initConfig()
    {
        parent::initConfig();
        $this->mergeConfig([
            'nullable'               => true, // TRUE if property value can be NULL, FALSE if not
            'default'                => null, // Default value for the property
            'update_notify_listener' => null, // Listener of property update notifications
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function onConfigChange($name, $value)
    {
        switch ($name) {
            case 'nullable':
                $this->nullable = $value;
                break;
        }
    }
}
