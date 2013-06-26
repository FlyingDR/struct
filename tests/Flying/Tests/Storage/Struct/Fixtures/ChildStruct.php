<?php

namespace Flying\Tests\Storage\Struct\Fixtures;

use Flying\Struct\StorableStruct;
use Flying\Tests\Tools\CallbackLog;
use Flying\Tests\Tools\CallbackTrackingInterface;

/**
 * Child structure to test multi-level structures
 *
 * @property boolean $x
 * @Struct\Boolean(name="x", default=false)
 * @property int $y
 * @Struct\Int(name="y", default=345)
 * @property string $z
 * @Struct\String(name="z", default="string")
 */
class ChildStruct extends StorableStruct implements CallbackTrackingInterface
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


}
