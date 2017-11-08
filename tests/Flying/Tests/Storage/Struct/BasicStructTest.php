<?php

namespace Flying\Tests\Storage\Struct;

use Flying\Struct\ConfigurationManager;
use Flying\Struct\Storage\Storage;
use Flying\Tests\Storage\Struct\Fixtures\BasicStruct;
use Flying\Tests\Struct\Common\BasicStructTest as CommonBasicStructTest;

/**
 * @method BasicStruct getTestStruct($contents = null, $config = null)
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
        static::assertArrayHasKey('Flying\Struct\StructInterface', $interfaces);
        static::assertArrayHasKey('Flying\Struct\Storage\StorableInterface', $interfaces);
        static::assertArrayHasKey('Countable', $interfaces);
        static::assertArrayHasKey('Iterator', $interfaces);
        static::assertArrayHasKey('RecursiveIterator', $interfaces);
        static::assertArrayHasKey('ArrayAccess', $interfaces);
        static::assertArrayHasKey('Serializable', $interfaces);
        static::assertArrayHasKey('Flying\Struct\Common\ComplexPropertyInterface', $interfaces);
        static::assertArrayHasKey('Flying\Struct\Common\UpdateNotifyListenerInterface', $interfaces);
    }

    public function testExplicitStorageSetting()
    {
        $storage = new Storage();
        $struct = $this->getTestStruct(null, [
            'storage' => $storage,
        ]);
        static::assertNotEquals($struct->getConfig('storage'), ConfigurationManager::getConfiguration()->getStorage());
        static::assertTrue($storage->has($struct));
        static::assertFalse(ConfigurationManager::getConfiguration()->getStorage()->has($struct));
    }

    public function testStructureShouldRegisterItselfIntoStorage()
    {
        $struct = $this->getTestStruct();
        $storage = ConfigurationManager::getConfiguration()->getStorage();
        static::assertTrue($storage->has($struct));
    }

    public function testStructureModificationsShouldMarkItAsDirtyInStorage()
    {
        $struct = $this->getTestStruct();
        $storage = ConfigurationManager::getConfiguration()->getStorage();
        static::assertFalse($storage->isDirty($struct));
        $struct->first = false;
        static::assertTrue($storage->isDirty($struct));
    }

    public function testStructureShouldTakeItsInitialContentsFromStorageBackend()
    {
        $s1 = $this->getTestStruct();
        $storage = ConfigurationManager::getConfiguration()->getStorage();
        $modified = 'modified value';
        $s1->fourth = $modified;
        static::assertEquals($modified, $s1->fourth);
        // Check that structure modifications doesn't propagated into storage automatically
        $s2 = $this->getTestStruct();
        static::assertNotEquals($s1->fourth, $s2->fourth);
        $storage->flush();
        $s3 = $this->getTestStruct();
        static::assertEquals($modified, $s3->fourth);
    }

    public function testStructureShouldBeMarkedAsDirtyUponDirectChangeOfItsProperty()
    {
        $struct = $this->getTestStruct();
        $storage = ConfigurationManager::getConfiguration()->getStorage();
        static::assertFalse($storage->isDirty($struct));
        $property = $struct->getProperty('fourth');
        $property->setValue('modified value');
        static::assertTrue($storage->isDirty($struct));
    }
}
