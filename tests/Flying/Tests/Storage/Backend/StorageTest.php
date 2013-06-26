<?php

namespace Flying\Tests\Storage\Backend;

use Flying\Struct\Configuration;
use Flying\Struct\ConfigurationManager;
use Flying\Struct\Storage\ArrayBackend;
use Flying\Struct\Storage\BackendInterface;
use Flying\Struct\Storage\StorableInterface;
use Flying\Struct\Storage\Storage;
use Flying\Tests\TestCaseUsingConfiguration;
use Mockery;

class StorageTest extends TestCaseUsingConfiguration
{

    public function testBasicOperations()
    {
        $object = $this->getStorableMock();
        $key = $object->getStorageKey();
        $storage = $this->getTestStorage();
        $this->assertFalse($storage->has($object));
        $storage->register($object);
        $this->assertTrue($storage->has($object));
        $storage->flush();
        $stored = $storage->load($key);
        $this->assertEquals($stored, $object->toStorage());
        $storage->remove($object);
        $this->assertFalse($storage->has($object));
    }

    public function testBackendShouldBeTakenFromConfigurationByDefault()
    {
        $storage = $this->getTestStorage();
        $this->assertEquals($storage->getBackend(), ConfigurationManager::getConfiguration()->getStorageBackend());
    }

    public function testExplicitSettingOwnBackend()
    {
        $storage = $this->getTestStorage();
        $backend = new ArrayBackend();
        $storage->setBackend($backend);
        $this->assertEquals($backend, $storage->getBackend());
    }

    public function testMultipleObjectsWithSameStorageKeyShouldBeRegisteredSeparately()
    {
        $storage = $this->getTestStorage();
        $m1 = $this->getStorableMock();
        $storage->register($m1);
        $m2 = $this->getStorableMock();
        $storage->register($m2);
        $this->assertTrue($storage->has($m1));
        $this->assertTrue($storage->has($m2));
        $storage->remove($m1);
        $this->assertFalse($storage->has($m1));
        $this->assertTrue($storage->has($m2));
    }

    public function testDirtyFlagOperations()
    {
        $storage = $this->getTestStorage();
        $object = $this->getStorableMock();
        $storage->register($object);
        $this->assertFalse($storage->isDirty($object));
        $storage->markAsDirty($object);
        $this->assertTrue($storage->isDirty($object));
        $storage->markAsNotDirty($object);
        $this->assertFalse($storage->isDirty($object));
    }

    public function testStorageFlushing()
    {
        $storage = $this->getTestStorage();
        $mock = $this->getStorableMock();
        $backend = Mockery::mock('Flying\Struct\Storage\BackendInterface');
        $backend->shouldReceive('has')->once()->ordered()->with($mock->getStorageKey())->andReturn(false)->getMock();
        $backend->shouldReceive('save')->once()->ordered()->with($mock->getStorageKey(), $mock->toStorage())->getMock();
        /** @var $backend BackendInterface */
        $storage->setBackend($backend);
        $storage->register($mock);
        $storage->flush();
    }

    public function testObjectsThatAreNotStoredInBackendShouldBeStoredInAnyCase()
    {
        $storage = $this->getTestStorage();
        $mock = $this->getStorableMock();
        $storage->register($mock);
        $this->assertFalse($storage->getBackend()->has($mock->getStorageKey()));
        $storage->markAsNotDirty($mock);
        $storage->flush();
        $this->assertTrue($storage->getBackend()->has($mock->getStorageKey()));
    }

    public function testObjectsThatAvailableInBackendShouldNotBeStoredIfNotDirty()
    {
        $storage = $this->getTestStorage();
        $mock = $this->getStorableMock();
        $key = $mock->getStorageKey();
        $backend = Mockery::mock('Flying\Struct\Storage\BackendInterface');
        $backend->shouldReceive('has')->once()->ordered()->with($key)->andReturn(true)->getMock();
        $backend->shouldReceive('save')->times(0)->getMock();
        /** @var $backend BackendInterface */
        $storage->setBackend($backend);
        $storage->register($mock);
        $storage->markAsNotDirty($mock);
        $storage->flush();
    }

    public function testFlushingMultipleObjectsWithSameStorageKeyShouldThrowException()
    {
        $storage = $this->getTestStorage();
        $m1 = $this->getStorableMock();
        $storage->register($m1);
        $storage->markAsDirty($m1);
        $m2 = clone $m1;
        $storage->register($m2);
        $storage->markAsDirty($m2);
        $this->setExpectedException('\RuntimeException');
        $storage->flush();
    }

    /**
     * Get new instance of class being tested
     *
     * @return Storage
     */
    protected function getTestStorage()
    {
        return new Storage();
    }

    /**
     * Get instance of mock object for StorableInterface
     *
     * @return StorableInterface
     */
    protected function getStorableMock()
    {
        /** @var $mock StorableInterface */
        $mock = Mockery::mock('Flying\Struct\Storage\StorableInterface')
            ->shouldReceive('getStorageKey')->andReturn('myTestKey')->getMock()
            ->shouldReceive('toStorage')->andReturn(array('myStorageRepresentation'))
            ->getMock();
        return $mock;
    }

}
