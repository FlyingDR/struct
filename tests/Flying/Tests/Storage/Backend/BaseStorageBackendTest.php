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
        $this->assertFalse($backend->has($this->testKey));
        $this->assertNull($backend->load($this->testKey));
        $backend->save($this->testKey, $this->testContents);
        $this->assertTrue($backend->has($this->testKey));
        $this->assertEquals($this->testContents, $backend->load($this->testKey));
        $backend->remove($this->testKey);
        $this->assertFalse($backend->has($this->testKey));
        $this->assertNull($backend->load($this->testKey));
    }

    public function testClearingBackend()
    {
        $backend = $this->getTestBackend();
        $this->assertFalse($backend->has('a'));
        $this->assertFalse($backend->has('b'));
        $backend->save('a', 'aaa');
        $backend->save('b', 'bbb');
        $this->assertTrue($backend->has('a'));
        $this->assertTrue($backend->has('b'));
        $backend->remove('b');
        $backend->save('c', 'ccc');
        $this->assertTrue($backend->has('a'));
        $this->assertFalse($backend->has('b'));
        $this->assertTrue($backend->has('c'));
        $backend->clear();
        $this->assertFalse($backend->has('a'));
        $this->assertFalse($backend->has('b'));
        $this->assertFalse($backend->has('c'));
    }

    /**
     * Get storage backend that needs to be tested
     *
     * @return BackendInterface
     */
    abstract protected function getTestBackend();

}
