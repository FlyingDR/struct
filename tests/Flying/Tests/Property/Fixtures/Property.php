<?php

namespace Flying\Tests\Property\Fixtures;

use Flying\Struct\Property\Property as BaseProperty;
use Flying\Tests\Tools\CallbackLog;
use Flying\Tests\Tools\CallbackTrackingInterface;

/**
 * Test property class to allow testing of various callbacks and notification calls
 */
class Property extends BaseProperty implements CallbackTrackingInterface
{
    /**
     * Available callback loggers
     * @var array
     */
    private $cbLogs = array();

    /**
     * Set logger for defined method
     *
     * @param string $method        Method name
     * @param CallbackLog $logger
     * @return void
     */
    public function setCallbackLogger($method, CallbackLog $logger)
    {
        $this->cbLogs[$method] = $logger;
    }

    /**
     * Log call to callback
     *
     * @param string $method    Method name
     * @param array $args       Method call arguments
     * @return void
     */
    protected function logCallbackCall($method, array $args)
    {
        if (array_key_exists($method, $this->cbLogs)) {
            /** @var $logger CallbackLog */
            $logger = $this->cbLogs[$method];
            $logger->add($method, $args);
        }
    }

    /**
     * Normalize given value to make it compatible with property requirements
     *
     * @param mixed $value  Given property value (passed by reference)
     * @return mixed        TRUE if value can be accepted, FALSE otherwise
     */
    protected function normalize(&$value)
    {
        $this->logCallbackCall(__FUNCTION__, func_get_args());
        return parent::normalize($value);
    }

    /**
     * {@inheritdoc}
     */
    protected function onChange()
    {
        $this->logCallbackCall(__FUNCTION__, func_get_args());
        parent::onChange();
    }

    /**
     * {@inheritdoc}
     */
    protected function onInvalidValue($value)
    {
        $this->logCallbackCall(__FUNCTION__, func_get_args());
        parent::onInvalidValue($value);
    }
}
