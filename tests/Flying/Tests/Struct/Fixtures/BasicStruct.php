<?php

namespace Flying\Tests\Struct\Fixtures;

use Flying\Struct\Common\StructItemInterface;
use Flying\Struct\Struct;
use Flying\Tests\Tools\CallbackLog;
use Flying\Tests\Tools\CallbackTrackingInterface;

/**
 * Basic test structure
 *
 * @property boolean $first
 * @Struct\Boolean(name="first", default=true)
 * @property int $second
 * @Struct\Int(name="second", nullable=false, default=100, min=10, max=1000)
 * @property string $third
 * @Struct\String(name="third")
 * @property string $fourth
 * @Struct\Property(name="fourth", type="string", default="some value")
 */
class BasicStruct extends Struct implements CallbackTrackingInterface
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

    public function getExpectedContents()
    {
        return (array(
            'first'  => true,
            'second' => 100,
            'third'  => null,
            'fourth' => 'some value',
        ));
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
