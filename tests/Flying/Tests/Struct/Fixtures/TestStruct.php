<?php

namespace Flying\Tests\Struct\Fixtures;

use Flying\Struct\Common\StructItemInterface;
use Flying\Struct\Struct;
use Flying\Tests\Tools\CallbackLog;
use Flying\Tests\Tools\CallbackTrackingInterface;

abstract class TestStruct extends Struct implements CallbackTrackingInterface
{
    /**
     * Available callback loggers
     * @var array
     */
    private $_cbLogs = array();

    /**
     * Set logger for defined method
     *
     * @param string $method        Method name
     * @param CallbackLog $logger
     * @return void
     */
    public function setCallbackLogger($method, CallbackLog $logger)
    {
        $this->_cbLogs[$method] = $logger;
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
        if (array_key_exists($method, $this->_cbLogs)) {
            /** @var $logger CallbackLog */
            $logger = $this->_cbLogs[$method];
            $logger->add($method, $args);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getMissed($name, $default)
    {
        $this->logCallbackCall(__FUNCTION__, func_get_args());
        return parent::getMissed($name, $default);
    }

    /**
     * {@inheritdoc}
     */
    protected function setMissed($name, $value)
    {
        $this->logCallbackCall(__FUNCTION__, func_get_args());
        parent::setMissed($name, $value);
    }

    /**
     * {@inheritdoc}
     */
    protected function onChange($name)
    {
        $this->logCallbackCall(__FUNCTION__, func_get_args());
        parent::onChange($name);
    }

    /**
     * {@inheritdoc}
     */
    public function updateNotify(StructItemInterface $property)
    {
        $this->logCallbackCall(__FUNCTION__, func_get_args());
        parent::updateNotify($property);
    }

}
