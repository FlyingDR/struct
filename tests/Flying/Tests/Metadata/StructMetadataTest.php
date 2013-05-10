<?php

namespace Flying\Tests\Metadata;

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
        $this->assertTrue($metadata->hasProperty($p1->getName()));
        $this->assertFalse($metadata->hasProperty($p2->getName()));
        $this->assertEquals($metadata->getProperty($p1->getName()), $p1);

        $properties = $metadata->getProperties();
        $this->assertInternalType('array', $properties);
        $this->assertEquals(1, sizeof($properties));
        $this->assertArrayHasKey($p1->getName(), $properties);

        $metadata->addProperty($p2);
        $this->assertTrue($metadata->hasProperty($p1->getName()));
        $this->assertTrue($metadata->hasProperty($p2->getName()));
        $this->assertEquals($metadata->getProperty($p1->getName()), $p1);
        $this->assertEquals($metadata->getProperty($p2->getName()), $p2);

        $properties = $metadata->getProperties();
        $this->assertInternalType('array', $properties);
        $this->assertEquals(2, sizeof($properties));
        $this->assertArrayHasKey($p1->getName(), $properties);
        $this->assertArrayHasKey($p2->getName(), $properties);

        $metadata->removeProperty($p1->getName());
        $this->assertFalse($metadata->hasProperty($p1->getName()));
        $this->assertTrue($metadata->hasProperty($p2->getName()));
        $this->assertEquals($metadata->getProperty($p2->getName()), $p2);

        $properties = $metadata->getProperties();
        $this->assertInternalType('array', $properties);
        $this->assertEquals(1, sizeof($properties));
        $this->assertArrayNotHasKey($p1->getName(), $properties);
        $this->assertArrayHasKey($p2->getName(), $properties);

        $metadata->clearProperties();
        $this->assertFalse($metadata->hasProperty($p1->getName()));
        $this->assertFalse($metadata->hasProperty($p2->getName()));

        $properties = $metadata->getProperties();
        $this->assertInternalType('array', $properties);
        $this->assertEquals(0, sizeof($properties));
        $this->assertArrayNotHasKey($p1->getName(), $properties);
        $this->assertArrayNotHasKey($p2->getName(), $properties);
    }

    public function testFillingObjectFromConstructor()
    {
        $class = get_class($this->getMetadataObject());
        $properties = array(
            new PropertyMetadata('p1'),
            new PropertyMetadata('p2'),
        );
        /** @var $metadata StructMetadata */
        $metadata = new $class($this->_name, $this->_class, $this->_config, $properties);
        $this->assertEquals($metadata->getName(), $this->_name);
        $this->assertEquals($metadata->getClass(), $this->_class);
        $this->assertTrue($metadata->getConfig() === $this->_config);
        /** @var $property PropertyMetadata */
        foreach ($properties as $property) {
            $this->assertTrue($metadata->getProperty($property->getName()) === $property);
        }
    }

    public function testGettingInvalidProperty()
    {
        $this->setExpectedException('\Flying\Struct\Exception');
        $metadata = $this->getMetadataObject();
        $metadata->getProperty('nonexisting');
    }

    /**
     * @return StructMetadata
     */
    protected function getMetadataObject()
    {
        return new StructMetadata();
    }

}
