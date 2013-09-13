<?php

namespace Flying\Tests\Storage\Struct;

use Flying\Struct\ConfigurationManager;
use Flying\Tests\Struct\Common\MultiLevelStructTest as CommonMultiLevelStructTest;

/**
 * @method \Flying\Tests\Storage\Struct\Fixtures\MultiLevelStruct getTestStruct($contents = null, $config = null)
 */
class MultiLevelStructTest extends CommonMultiLevelStructTest
{
    /**
     * Namespace for fixtures structures
     * @var string
     */
    protected $fixturesNs = 'Flying\Tests\Storage\Struct\Fixtures';
    /**
     * Name of fixture class to test
     * @var string
     */
    protected $fixtureClass = 'Flying\Tests\Storage\Struct\Fixtures\MultiLevelStruct';

    public function testChildStructureShouldNotRegisterItselfIntoStorage()
    {
        $struct = $this->getTestStruct();
        $storage = ConfigurationManager::getConfiguration()->getStorage();
        $this->assertFalse($storage->has($struct->child));
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
