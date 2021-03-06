<?php

namespace Flying\Tests\Storage\Struct\Fixtures;

use Flying\Struct\Common\SimplePropertyInterface;
use Flying\Struct\StorableStruct;
use Flying\Tests\Tools\CallbackLog;
use Flying\Tests\Tools\CallbackTrackingInterface;

abstract class TestStruct extends StorableStruct implements CallbackTrackingInterface
{
    /**
     * Available callback loggers
     *
     * @var array
     */
    private $cbLogs = [];

    /**
     * Set logger for defined method
     *
     * @param string $method Method name
     * @param CallbackLog $logger
     * @return void
     */
    public function setCallbackLogger($method, CallbackLog $logger)
    {
        $this->cbLogs[$method] = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function updateNotify(SimplePropertyInterface $property)
    {
        $this->logCallbackCall(__FUNCTION__, func_get_args());
        parent::updateNotify($property);
    }

    /**
     * Get contents that are expected to be returned by this structure
     *
     * @return array
     */
    abstract public function getExpectedContents();

    /**
     * {@inheritdoc}
     */
    protected function getMissed($name, $default)
    {
        $this->logCallbackCall(__FUNCTION__, func_get_args());
        return parent::getMissed($name, $default);
    }

    /**
     * Log call to callback
     *
     * @param string $method Method name
     * @param array $args    Method call arguments
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
}
