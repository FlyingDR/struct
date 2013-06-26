<?php

namespace Flying\Tests\Storage\Backend;

use Flying\Struct\Storage\BackendInterface;
use Flying\Struct\Storage\CacheBackend;

class CacheBackendTest extends BaseStorageBackendTest
{

    public function testClearingBackend()
    {
        // Cache backend doesn't have support for cache clearing
    }

    /**
     * Get storage backend that needs to be tested
     *
     * @return BackendInterface
     */
    protected function getTestBackend()
    {
        return new CacheBackend();
    }

}
