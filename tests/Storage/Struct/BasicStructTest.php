<?php

namespace Flying\Tests\Storage\Struct;

use Flying\Struct\Common\ComplexPropertyInterface;
use Flying\Struct\Common\UpdateNotifyListenerInterface;
use Flying\Struct\ConfigurationManager;
use Flying\Struct\StorableStruct;
use Flying\Struct\Storage\StorableInterface;
use Flying\Struct\Storage\Storage;
use Flying\Struct\StructInterface;
use Flying\Tests\Storage\Struct\Fixtures\BasicStruct;
use Flying\Tests\Struct\Common\BasicStructTest as CommonBasicStructTest;

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
    protected $fixtureClass = BasicStruct::class;

    public function testStructureInterfaces()
    {
        $reflection = new \ReflectionClass(StorableStruct::class);
        $interfaces = $reflection->getInterfaces();
        static::assertArrayHasKey(StructInterface::class, $interfaces);
        static::assertArrayHasKey(StorableInterface::class, $interfaces);
        static::assertArrayHasKey('Countable', $interfaces);
        static::assertArrayHasKey('Iterator', $interfaces);
        static::assertArrayHasKey('RecursiveIterator', $interfaces);
        static::assertArrayHasKey('ArrayAccess', $interfaces);
        static::assertArrayHasKey('Serializable', $interfaces);
        static::assertArrayHasKey(ComplexPropertyInterface::class, $interfaces);
        static::assertArrayHasKey(UpdateNotifyListenerInterface::class, $interfaces);
    }

    public function testExplicitStorageSetting()
    {
        $storage = new Storage();
        /** @var BasicStruct $struct */
        $struct = $this->getTestStruct(null, [
            'storage' => $storage,
        ]);
        static::assertNotEquals($struct->getConfig('storage'), ConfigurationManager::getConfiguration()->getStorage());
        static::assertTrue($storage->has($struct));
        static::assertFalse(ConfigurationManager::getConfiguration()->getStorage()->has($struct));
    }

    public function testStructureShouldRegisterItselfIntoStorage()
    {
        /** @var BasicStruct $struct */
        $struct = $this->getTestStruct();
        $storage = ConfigurationManager::getConfiguration()->getStorage();
        static::assertTrue($storage->has($struct));
    }

    public function testStructureModificationsShouldMarkItAsDirtyInStorage()
    {
        /** @var BasicStruct $struct */
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
        /** @var BasicStruct $struct */
        $struct = $this->getTestStruct();
        $storage = ConfigurationManager::getConfiguration()->getStorage();
        static::assertFalse($storage->isDirty($struct));
        $property = $struct->getProperty('fourth');
        static::assertNotNull($property);
        $property->setValue('modified value');
        static::assertTrue($storage->isDirty($struct));
    }
}
