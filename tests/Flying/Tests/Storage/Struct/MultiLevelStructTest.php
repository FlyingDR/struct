<?php

namespace Flying\Tests\Storage\Struct;

use Flying\Struct\ConfigurationManager;
use Flying\Struct\Storage\StorageInterface;
use Flying\Tests\Storage\Struct\Fixtures\MultiLevelStruct;
use Flying\Tests\Struct\Common\MultiLevelStructTest as CommonMultiLevelStructTest;
use Mockery;

/**
 * @method MultiLevelStruct getTestStruct($contents = null, $config = null)
 */
class MultiLevelStructTest extends CommonMultiLevelStructTest
{
    /**
     * Namespace for fixtures structures
     *
     * @var string
     */
    protected $fixturesNs = 'Flying\Tests\Storage\Struct\Fixtures';
    /**
     * Name of fixture class to test
     *
     * @var string
     */
    protected $fixtureClass = 'Flying\Tests\Storage\Struct\Fixtures\MultiLevelStruct';

    public function testChildStructureShouldNotCommunicateWithStorage()
    {
        $storage = Mockery::mock('Flying\Struct\Storage\StorageInterface');
        $storage->shouldReceive('load')->once()->ordered()
            ->with('/^' . str_replace('\\', '_', $this->fixtureClass) . '/')->andReturnNull()->getMock();
        $storage->shouldReceive('register')->once()->ordered()
            ->with(Mockery::type($this->fixtureClass))->andReturn(Mockery::self())->getMock();
        $storage->shouldReceive('markAsDirty')->once()->ordered()
            ->with(Mockery::type($this->fixtureClass))->andReturn(Mockery::self())->getMock();
        /** @var $storage StorageInterface */
        ConfigurationManager::getConfiguration()->setStorage($storage);
        $struct = $this->getTestStruct(array(
            'b'     => false,
            'i'     => 777,
            's'     => 'something',
            'child' => array(
                'x' => true,
                'y' => 888,
                'z' => 'child',
            ),
        ));
        $struct->child->x = false;
    }

    public function testChildStructureModificationsShouldMarkWholeStructureAsDirtyInStorage()
    {
        $struct = $this->getTestStruct();
        $storage = ConfigurationManager::getConfiguration()->getStorage();
        static::assertFalse($storage->isDirty($struct));
        $struct->child->x = false;
        static::assertTrue($storage->isDirty($struct));
    }

    public function testStructureShouldTakeItsInitialContentsFromStorageBackend()
    {
        $s1 = $this->getTestStruct();
        $storage = ConfigurationManager::getConfiguration()->getStorage();
        $modified = 'modified value';
        $s1->child->z = $modified;
        static::assertEquals($modified, $s1->child->z);
        // Check that structure modifications doesn't propagated into storage automatically
        $s2 = $this->getTestStruct();
        static::assertNotEquals($s1->child->z, $s2->child->z);
        $storage->flush();
        $s3 = $this->getTestStruct();
        static::assertEquals($modified, $s3->child->z);
    }
}
