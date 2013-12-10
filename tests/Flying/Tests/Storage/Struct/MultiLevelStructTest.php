<?php

namespace Flying\Tests\Storage\Struct;

use Flying\Struct\ConfigurationManager;
use Flying\Tests\Struct\Common\MultiLevelStructTest as CommonMultiLevelStructTest;
use Mockery;

/**
 * @method \Flying\Tests\Storage\Struct\Fixtures\MultiLevelStruct getTestStruct($contents = null, $config = null)
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
        $this->getTestStruct(array(
            'b'     => false,
            'i'     => 777,
            's'     => 'something',
            'child' => array(
                'x' => true,
                'y' => 888,
                'z' => 'child',
            ),
        ), array('storage' => $storage));
    }

    public function testChildStructureModificationsShouldMarkWholeStructureAsDirtyInStorage()
    {
        $struct = $this->getTestStruct();
        $storage = ConfigurationManager::getConfiguration()->getStorage();
        $this->assertFalse($storage->isDirty($struct));
        $struct->child->x = false;
        $this->assertTrue($storage->isDirty($struct));
    }

    public function testStructureShouldTakeItsInitialContentsFromStorageBackend()
    {
        $s1 = $this->getTestStruct();
        $storage = ConfigurationManager::getConfiguration()->getStorage();
        $modified = 'modified value';
        $s1->child->z = $modified;
        $this->assertEquals($modified, $s1->child->z);
        // Check that structure modifications doesn't propagated into storage automatically
        $s2 = $this->getTestStruct();
        $this->assertNotEquals($s1->child->z, $s2->child->z);
        $storage->flush();
        $s3 = $this->getTestStruct();
        $this->assertEquals($modified, $s3->child->z);
    }
}
