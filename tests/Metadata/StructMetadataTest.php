<?php

namespace Flying\Tests\Metadata;

use Flying\Struct\Exception;
use Flying\Struct\Metadata\PropertyMetadata;
use Flying\Struct\Metadata\StructMetadata;

class StructMetadataTest extends BaseMetadataTest
{
    public function testPropertyOperations()
    {
        $metadata = $this->getMetadataObject();

        $p1 = new PropertyMetadata('p1');
        $p2 = new PropertyMetadata('p2');

        $metadata->addProperty($p1);
        static::assertTrue($metadata->hasProperty($p1->getName()));
        static::assertFalse($metadata->hasProperty($p2->getName()));
        static::assertEquals($metadata->getProperty($p1->getName()), $p1);

        $properties = $metadata->getProperties();
        static::assertInternalType('array', $properties);
        static::assertCount(1, $properties);
        static::assertArrayHasKey($p1->getName(), $properties);

        $metadata->addProperty($p2);
        static::assertTrue($metadata->hasProperty($p1->getName()));
        static::assertTrue($metadata->hasProperty($p2->getName()));
        static::assertEquals($metadata->getProperty($p1->getName()), $p1);
        static::assertEquals($metadata->getProperty($p2->getName()), $p2);

        $properties = $metadata->getProperties();
        static::assertInternalType('array', $properties);
        static::assertCount(2, $properties);
        static::assertArrayHasKey($p1->getName(), $properties);
        static::assertArrayHasKey($p2->getName(), $properties);

        $metadata->removeProperty($p1->getName());
        static::assertFalse($metadata->hasProperty($p1->getName()));
        static::assertTrue($metadata->hasProperty($p2->getName()));
        static::assertEquals($metadata->getProperty($p2->getName()), $p2);

        $properties = $metadata->getProperties();
        static::assertInternalType('array', $properties);
        static::assertCount(1, $properties);
        static::assertArrayNotHasKey($p1->getName(), $properties);
        static::assertArrayHasKey($p2->getName(), $properties);

        $metadata->clearProperties();
        static::assertFalse($metadata->hasProperty($p1->getName()));
        static::assertFalse($metadata->hasProperty($p2->getName()));

        $properties = $metadata->getProperties();
        static::assertInternalType('array', $properties);
        static::assertCount(0, $properties);
        static::assertArrayNotHasKey($p1->getName(), $properties);
        static::assertArrayNotHasKey($p2->getName(), $properties);
    }

    /**
     * @return StructMetadata
     */
    protected function getMetadataObject()
    {
        return new StructMetadata();
    }

    public function testFillingObjectFromConstructor()
    {
        $class = get_class($this->getMetadataObject());
        $properties = [
            new PropertyMetadata('p1'),
            new PropertyMetadata('p2'),
        ];
        /** @var $metadata StructMetadata */
        $metadata = new $class($this->name, $this->class, $this->config, $properties);
        static::assertEquals($metadata->getName(), $this->name);
        static::assertEquals($metadata->getClass(), $this->class);
        static::assertSame($this->config, $metadata->getConfig());
        /** @var $property PropertyMetadata */
        foreach ($properties as $property) {
            static::assertSame($property, $metadata->getProperty($property->getName()));
        }
    }

    public function testGettingInvalidProperty()
    {
        $this->expectException(Exception::class);
        $metadata = $this->getMetadataObject();
        $metadata->getProperty('unavailable');
    }

    public function testSerializationOfStructureWithProperties()
    {
        // Test that information about structure properties is also serialized
        $metadata = $this->getMetadataObject();
        $p1 = new PropertyMetadata('p1');
        $p2 = new PropertyMetadata('p2');
        $metadata->addProperty($p1);
        $metadata->addProperty($p2);
        $serialized = serialize($metadata);
        /** @var $new StructMetadata */
        $new = unserialize($serialized);
        static::assertNotSame($metadata, $new);
        static::assertInstanceOf(StructMetadata::class, $new);
        static::assertTrue($new->hasProperty('p1'));
        static::assertTrue($new->hasProperty('p2'));
    }

    public function testToArray()
    {
        $metadata = $this->getMetadataObject();
        $metadata->setName($this->name)
            ->setClass($this->class)
            ->setConfig($this->config);
        $expected = [
            'name'       => $this->name,
            'class'      => $this->class,
            'config'     => $this->config,
            'hash'       => $metadata->getHash(),
            'properties' => [],
        ];
        static::assertEquals($expected, $metadata->toArray());
    }

    public function testToArrayWithProperties()
    {
        $metadata = $this->getMetadataObject();
        $p1 = new PropertyMetadata('p1');
        $p2 = new PropertyMetadata('p2');
        $metadata->setName($this->name)
            ->setClass($this->class)
            ->setConfig($this->config)
            ->addProperty($p1)
            ->addProperty($p2);
        $expected = [
            'name'       => $this->name,
            'class'      => $this->class,
            'config'     => $this->config,
            'hash'       => $metadata->getHash(),
            'properties' => [
                'p1' => $p1->toArray(),
                'p2' => $p2->toArray(),
            ],
        ];
        static::assertEquals($expected, $metadata->toArray());
    }

    public function testHashUpdateOnPropertiesOperations()
    {
        $metadata = $this->getMetadataObject();
        $p1 = new PropertyMetadata('p1');
        $p2 = new PropertyMetadata('p2');
        $hash = $metadata->getHash();
        $metadata->addProperty($p1);
        static::assertNotEquals($hash, $metadata->getHash());
        $hash = $metadata->getHash();
        $metadata->addProperty($p2);
        static::assertNotEquals($hash, $metadata->getHash());
        $hash = $metadata->getHash();
        $metadata->removeProperty($p1->getName());
        static::assertNotEquals($hash, $metadata->getHash());
        $hash = $metadata->getHash();
        $metadata->clearProperties();
        static::assertNotEquals($hash, $metadata->getHash());
        $hash = $metadata->getHash();
        $metadata->setProperties([$p1, $p2]);
        static::assertNotEquals($hash, $metadata->getHash());
    }
}
