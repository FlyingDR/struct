<?php

namespace Flying\Tests\Metadata;

use Flying\Struct\Metadata\MetadataInterface;
use Flying\Tests\TestCase;

abstract class BaseMetadataTest extends TestCase
{
    protected $name = 'test_name';
    protected $class = 'test_class';
    protected $config = [
        'abc' => 123,
        'xyz' => 456,
    ];

    public function testBasicOperations()
    {
        $metadata = $this->getMetadataObject();

        $metadata->setName($this->name);
        static::assertEquals($metadata->getName(), $this->name);

        $metadata->setClass($this->class);
        static::assertEquals($metadata->getClass(), $this->class);

        $metadata->setConfig($this->config);
        static::assertSame($this->config, $metadata->getConfig());
    }

    /**
     * @return MetadataInterface
     */
    abstract protected function getMetadataObject();

    public function testFillingObjectFromConstructor()
    {
        $class = get_class($this->getMetadataObject());
        /** @var $metadata MetadataInterface */
        $metadata = new $class($this->name, $this->class, $this->config);
        static::assertEquals($metadata->getName(), $this->name);
        static::assertEquals($metadata->getClass(), $this->class);
        static::assertSame($this->config, $metadata->getConfig());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Property name must be a string
     */
    public function testSettingInvalidName()
    {
        $metadata = $this->getMetadataObject();
        /** @noinspection PhpParamsInspection */
        $metadata->setName(['test_name']);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Property class name must be a string
     */
    public function testSettingInvalidClass()
    {
        $metadata = $this->getMetadataObject();
        /** @noinspection PhpParamsInspection */
        $metadata->setClass(['test_class']);
    }

    public function testFluentInterface()
    {
        $metadata = $this->getMetadataObject();
        $result = $metadata->setName($this->name)
            ->setClass($this->class)
            ->setConfig($this->config);
        static::assertSame($metadata, $result);
        static::assertEquals($metadata->getName(), $this->name);
        static::assertEquals($metadata->getClass(), $this->class);
        static::assertEquals($metadata->getConfig(), $this->config);
    }

    public function testEmptyObjectSerialization()
    {
        $metadata = $this->getMetadataObject();
        $class = get_class($metadata);
        $data = serialize($metadata);

        /** @var $new MetadataInterface */
        $new = unserialize($data);
        static::assertInstanceOf($class, $new);
        static::assertNotSame($metadata, $new);
    }

    public function testFilledObjectSerialization()
    {
        $metadata = $this->getMetadataObject();
        $class = get_class($metadata);
        $metadata->setName($this->name)
            ->setClass($this->class)
            ->setConfig($this->config);
        $data = serialize($metadata);

        /** @var $new MetadataInterface */
        $new = unserialize($data);
        static::assertInstanceOf($class, $new);
        static::assertNotSame($metadata, $new);
        static::assertEquals($new->getName(), $this->name);
        static::assertEquals($new->getClass(), $this->class);
        static::assertEquals($new->getConfig(), $this->config);
    }

    public function testUnserializationOfInvalidSerializationData()
    {
        $metadata = $this->getMetadataObject();
        $class = get_class($metadata);
        $data = sprintf('C:%d:"%s":0:{}', strlen($class), $class);
        $new = unserialize($data);
        static::assertInstanceOf($class, $new);
        static::assertNotSame($metadata, $new);
        static::assertNull($new->getName());
        static::assertNull($new->getClass());
        static::assertEmpty($new->getConfig());
    }

    public function testToArray()
    {
        $metadata = $this->getMetadataObject();
        $metadata->setName($this->name)
            ->setClass($this->class)
            ->setConfig($this->config);
        $expected = [
            'name'   => $this->name,
            'class'  => $this->class,
            'config' => $this->config,
            'hash'   => $metadata->getHash(),
        ];
        static::assertEquals($expected, $metadata->toArray());
    }

    public function testHashUpdateOnChange()
    {
        $metadata = $this->getMetadataObject();
        $hash = $metadata->getHash();
        $metadata->setName($this->name);
        static::assertNotEquals($hash, $metadata->getHash());
        $hash = $metadata->getHash();
        $metadata->setClass($this->class);
        static::assertNotEquals($hash, $metadata->getHash());
        $hash = $metadata->getHash();
        $metadata->setConfig($this->config);
        static::assertNotEquals($hash, $metadata->getHash());
    }
}
