<?php

namespace Flying\Tests\Metadata;

use Flying\Struct\Metadata\MetadataInterface;

abstract class BaseMetadataTest extends \PHPUnit_Framework_TestCase
{
    protected $_name = 'test_name';
    protected $_class = 'test_class';
    protected $_config = array(
        'abc' => 123,
        'xyz' => 456,
    );

    public function testBasicOperations()
    {
        $metadata = $this->getMetadataObject();

        $metadata->setName($this->_name);
        $this->assertEquals($metadata->getName(), $this->_name);

        $metadata->setClass($this->_class);
        $this->assertEquals($metadata->getClass(), $this->_class);

        $metadata->setConfig($this->_config);
        $this->assertTrue($metadata->getConfig() === $this->_config);
    }

    public function testFillingObjectFromConstructor()
    {
        $class = get_class($this->getMetadataObject());
        /** @var $metadata MetadataInterface */
        $metadata = new $class($this->_name, $this->_class, $this->_config);
        $this->assertEquals($metadata->getName(), $this->_name);
        $this->assertEquals($metadata->getClass(), $this->_class);
        $this->assertTrue($metadata->getConfig() === $this->_config);
    }

    public function testSettingInvalidName()
    {
        $this->setExpectedException('\InvalidArgumentException', 'Property name must be a string');
        $metadata = $this->getMetadataObject();
        $metadata->setName(array('test_name'));
    }

    public function testSettingInvalidClass()
    {
        $this->setExpectedException('\InvalidArgumentException', 'Property class name must be a string');
        $metadata = $this->getMetadataObject();
        $metadata->setClass(array('test_class'));
    }

    public function testFluentInterface()
    {
        $metadata = $this->getMetadataObject();
        $result = $metadata->setName($this->_name)
            ->setClass($this->_class)
            ->setConfig($this->_config);
        $this->assertTrue($metadata === $result);
        $this->assertEquals($metadata->getName(), $this->_name);
        $this->assertEquals($metadata->getClass(), $this->_class);
        $this->assertEquals($metadata->getConfig(), $this->_config);
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
        $metadata->setName($this->_name)
            ->setClass($this->_class)
            ->setConfig($this->_config);
        $data = serialize($metadata);

        /** @var $new MetadataInterface */
        $new = unserialize($data);
        $this->assertInstanceOf($class, $new);
        $this->assertFalse($metadata === $new);
        $this->assertEquals($new->getName(), $this->_name);
        $this->assertEquals($new->getClass(), $this->_class);
        $this->assertEquals($new->getConfig(), $this->_config);
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
        $metadata->setName($this->_name)
            ->setClass($this->_class)
            ->setConfig($this->_config);
        $expected = array(
            'name'   => $this->_name,
            'class'  => $this->_class,
            'config' => $this->_config,
        );
        $this->assertEquals($expected, $metadata->toArray());
    }

    /**
     * @return MetadataInterface
     */
    abstract protected function getMetadataObject();

}
