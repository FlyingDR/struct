<?php

namespace Flying\Tests\Storage\Backend;

use Flying\Struct\Storage\ArrayBackend;
use Flying\Struct\Storage\BackendInterface;

class ArrayBackendTest extends BaseStorageBackendTest
{

    /**
     * Get storage backend that needs to be tested
     *
     * @return BackendInterface
     */
    protected function getTestBackend()
    {
        return new ArrayBackend();
    }

}
