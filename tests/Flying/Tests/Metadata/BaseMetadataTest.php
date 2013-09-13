<?php

namespace Flying\Tests\Metadata;

use Flying\Struct\Metadata\MetadataInterface;
use Flying\Tests\TestCase;

abstract class BaseMetadataTest extends TestCase
{
    protected $name = 'test_name';
    protected $class = 'test_class';
    protected $config = array(
        'abc' => 123,
        'xyz' => 456,
    );

    public function testBasicOperations()
    {
        $metadata = $this->getMetadataObject();

        $metadata->setName($this->name);
        $this->assertEquals($metadata->getName(), $this->name);

        $metadata->setClass($this->class);
        $this->assertEquals($metadata->getClass(), $this->class);

        $metadata->setConfig($this->config);
        $this->assertTrue($metadata->getConfig() === $this->config);
    }

    public function testFillingObjectFromConstructor()
    {
        $class = get_class($this->getMetadataObject());
        /** @var $metadata MetadataInterface */
        $metadata = new $class($this->name, $this->class, $this->config);
        $this->assertEquals($metadata->getName(), $this->name);
        $this->assertEquals($metadata->getClass(), $this->class);
        $this->assertTrue($metadata->getConfig() === $this->config);
    }

    public function testSettingInvalidName()
    {
        $this->setExpectedException(
            '\InvalidArgumentException',
            'Property name must be a string'
        );
        $metadata = $this->getMetadataObject();
        $metadata->setName(array('test_name'));
    }

    public function testSettingInvalidClass()
    {
        $this->setExpectedException(
            '\InvalidArgumentException',
            'Property class name must be a string'
        );
        $metadata = $this->getMetadataObject();
        $metadata->setClass(array('test_class'));
    }

    public function testFluentInterface()
    {
        $metadata = $this->getMetadataObject();
        $result = $metadata->setName($this->name)
            ->setClass($this->class)
            ->setConfig($this->config);
        $this->assertTrue($metadata === $result);
        $this->assertEquals($metadata->getName(), $this->name);
        $this->assertEquals($metadata->getClass(), $this->class);
        $this->assertEquals($metadata->getConfig(), $this->config);
    }

    public function testEmptyObjectSerialization()
    {
        $metadata = $this->getMetadataObject();
        $class = get_class($metadata);
        $data = serialize($metadata);

        /** @var $new MetadataInterface */
        $new = unserialize($data);
        $this->assertInstanceOf($class, $new);
        $this->assertFalse($metadata === $new);
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
        $this->assertInstanceOf($class, $new);
        $this->assertFalse($metadata === $new);
        $this->assertEquals($new->getName(), $this->name);
        $this->assertEquals($new->getClass(), $this->class);
        $this->assertEquals($new->getConfig(), $this->config);
    }

    public function testUnserializationOfInvalidSerializationData()
    {
        $metadata = $this->getMetadataObject();
        $class = get_class($metadata);
        $data = sprintf('C:%d:"%s":0:{}', strlen($class), $class);
        $new = unserialize($data);
        $this->assertInstanceOf($class, $new);
        $this->assertFalse($metadata === $new);
        $this->assertNull($new->getName());
        $this->assertNull($new->getClass());
        $this->assertEmpty($new->getConfig());
    }

    public function testToArray()
    {
        $metadata = $this->getMetadataObject();
        $metadata->setName($this->name)
            ->setClass($this->class)
            ->setConfig($this->config);
        $expected = array(
            'name'   => $this->name,
            'class'  => $this->class,
            'config' => $this->config,
            'hash'   => $metadata->getHash(),
        );
        $this->assertEquals($expected, $metadata->toArray());
    }

    public function testHashUpdateOnChange()
    {
        $metadata = $this->getMetadataObject();
        $hash = $metadata->getHash();
        $metadata->setName($this->name);
        $this->assertNotEquals($hash, $metadata->getHash());
        $hash = $metadata->getHash();
        $metadata->setClass($this->class);
        $this->assertNotEquals($hash, $metadata->getHash());
        $hash = $metadata->getHash();
        $metadata->setConfig($this->config);
        $this->assertNotEquals($hash, $metadata->getHash());
    }

    /**
     * @return MetadataInterface
     */
    abstract protected function getMetadataObject();
}
