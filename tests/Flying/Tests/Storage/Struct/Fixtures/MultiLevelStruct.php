<?php

namespace Flying\Tests\Storage\Struct\Fixtures;

use Flying\Struct\Common\StructItemInterface;
use Flying\Struct\StorableStruct;
use Flying\Tests\Tools\CallbackLog;
use Flying\Tests\Tools\CallbackTrackingInterface;

/**
 * Multi-level structure with child structure
 *
 * @property boolean $b
 * @Struct\Boolean(name="b", default=true)
 * @property int $i
 * @Struct\Int(name="i", default=123)
 * @property string $s
 * @Struct\String(name="s", default="test")
 * @property ChildStruct $child
 * @Struct\Struct(name="child", class="ChildStruct")
 */
class MultiLevelStruct extends StorableStruct implements CallbackTrackingInterface
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
            'b'     => true,
            'i'     => 123,
            's'     => 'test',
            'child' => array(
                'x' => false,
                'y' => 345,
                'z' => 'string',
            ),
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
