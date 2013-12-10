<?php

namespace Flying\Tests\Storage\Struct;

use Flying\Struct\ConfigurationManager;
use Flying\Struct\Storage\Storage;
use Flying\Tests\Struct\Common\BasicStructTest as CommonBasicStructTest;

/**
 * @method \Flying\Tests\Storage\Struct\Fixtures\BasicStruct getTestStruct($contents = null, $config = null)
 */
class BasicStructTest extends CommonBasicStructTest
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
    protected $fixtureClass = 'Flying\Tests\Storage\Struct\Fixtures\BasicStruct';

    public function testStructureInterfaces()
    {
        $reflection = new \ReflectionClass('Flying\Struct\StorableStruct');
        $interfaces = $reflection->getInterfaces();
        $this->assertArrayHasKey('Flying\Struct\StructInterface', $interfaces);
        $this->assertArrayHasKey('Flying\Struct\Storage\StorableInterface', $interfaces);
        $this->assertArrayHasKey('Countable', $interfaces);
        $this->assertArrayHasKey('Iterator', $interfaces);
        $this->assertArrayHasKey('RecursiveIterator', $interfaces);
        $this->assertArrayHasKey('ArrayAccess', $interfaces);
        $this->assertArrayHasKey('Serializable', $interfaces);
        $this->assertArrayHasKey('Flying\Struct\Common\ComplexPropertyInterface', $interfaces);
        $this->assertArrayHasKey('Flying\Struct\Common\UpdateNotifyListenerInterface', $interfaces);
    }

    public function testExplicitStorageSetting()
    {
        $storage = new Storage();
        $struct = $this->getTestStruct(
            null,
            array(
                 'storage' => $storage,
            )
        );
        $this->assertNotEquals($struct->getConfig('storage'), ConfigurationManager::getConfiguration()->getStorage());
        $this->assertTrue($storage->has($struct));
        $this->assertFalse(ConfigurationManager::getConfiguration()->getStorage()->has($struct));
    }

    public function testStructureShouldRegisterItselfIntoStorage()
    {
        $struct = $this->getTestStruct();
        $storage = ConfigurationManager::getConfiguration()->getStorage();
        $this->assertTrue($storage->has($struct));
    }

    public function testStructureModificationsShouldMarkItAsDirtyInStorage()
    {
        $struct = $this->getTestStruct();
        $storage = ConfigurationManager::getConfiguration()->getStorage();
        $this->assertFalse($storage->isDirty($struct));
        $struct->first = false;
        $this->assertTrue($storage->isDirty($struct));
    }

    public function testStructureShouldTakeItsInitialContentsFromStorageBackend()
    {
        $s1 = $this->getTestStruct();
        $storage = ConfigurationManager::getConfiguration()->getStorage();
        $modified = 'modified value';
        $s1->fourth = $modified;
        $this->assertEquals($modified, $s1->fourth);
        // Check that structure modifications doesn't propagated into storage automatically
        $s2 = $this->getTestStruct();
        $this->assertNotEquals($s1->fourth, $s2->fourth);
        $storage->flush();
        $s3 = $this->getTestStruct();
        $this->assertEquals($modified, $s3->fourth);
    }
    
    public function testStructureShouldBeMarkedAsDirtyUponDirectChangeOfItsProperty()
    {
        $struct = $this->getTestStruct();
        $storage = ConfigurationManager::getConfiguration()->getStorage();
        $this->assertFalse($storage->isDirty($struct));
        $property = $struct->getProperty('fourth');
        $property->setValue('modified value');
        $this->assertTrue($storage->isDirty($struct));
    }
}
