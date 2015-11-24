<?php

namespace Flying\Tests\Storage\Backend;

use Flying\Struct\Storage\BackendInterface;
use Flying\Tests\TestCaseUsingConfiguration;

abstract class BaseStorageBackendTest extends TestCaseUsingConfiguration
{
    protected $testKey = 'test';
    protected $testContents = 'some contents';

    public function testBasicOperations()
    {
        $backend = $this->getTestBackend();
        static::assertFalse($backend->has($this->testKey));
        static::assertNull($backend->load($this->testKey));
        $backend->save($this->testKey, $this->testContents);
        static::assertTrue($backend->has($this->testKey));
        static::assertEquals($this->testContents, $backend->load($this->testKey));
        $backend->remove($this->testKey);
        static::assertFalse($backend->has($this->testKey));
        static::assertNull($backend->load($this->testKey));
    }

    public function testClearingBackend()
    {
        $backend = $this->getTestBackend();
        static::assertFalse($backend->has('a'));
        static::assertFalse($backend->has('b'));
        $backend->save('a', 'aaa');
        $backend->save('b', 'bbb');
        static::assertTrue($backend->has('a'));
        static::assertTrue($backend->has('b'));
        $backend->remove('b');
        $backend->save('c', 'ccc');
        static::assertTrue($backend->has('a'));
        static::assertFalse($backend->has('b'));
        static::assertTrue($backend->has('c'));
        $backend->clear();
        static::assertFalse($backend->has('a'));
        static::assertFalse($backend->has('b'));
        static::assertFalse($backend->has('c'));
    }

    /**
     * Get storage backend that needs to be tested
     *
     * @return BackendInterface
     */
    abstract protected function getTestBackend();

}
