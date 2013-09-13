<?php

namespace Flying\Tests\Tools;

/**
 * Interface for classes that can be used to test callback methods
 */
interface CallbackTrackingInterface
{
    /**
     * Set logger for defined method
     *
     * @param string $method        Method name
     * @param CallbackLog $logger
     * @return void
     */
    public function setCallbackLogger($method, CallbackLog $logger);
}
