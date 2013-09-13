<?php

namespace Flying\Tests\Property\Fixtures;

use Flying\Struct\Property\Collection as BaseCollection;
use Flying\Tests\Tools\CallbackLog;
use Flying\Tests\Tools\CallbackTrackingInterface;

class Collection extends BaseCollection implements CallbackTrackingInterface
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
     * {@inheritdoc}
     */
    protected function normalize(&$value, $key = null)
    {
        $this->logCallbackCall(__FUNCTION__, func_get_args());
        return parent::normalize($value, $key);
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
    protected function onInvalidValue($value, $key = null)
    {
        $this->logCallbackCall(__FUNCTION__, func_get_args());
        parent::onInvalidValue($value, $key);
    }
}
