<?php

namespace Flying\Tests\Annotation;

use Flying\Struct\Annotation\Struct\Boolean;
use Flying\Struct\Annotation\Struct\Property;
use Flying\Struct\Annotation\Struct\Struct;
use Flying\Tests\TestCase;

class StructTest extends TestCase
{

    public function testInheritance()
    {
        $annotation = new Struct(array('name' => 'test', 'class' => 'test'));
        $this->assertInstanceOf('\Flying\Struct\Annotation\Struct\Annotation', $annotation);
    }

    public function testBasicOperations()
    {
        $annotation = new Struct(array(
            'name'  => 'test',
            'class' => 'MyStruct',
            'abc'   => 'xyz',
        ));
        $this->assertEquals('test', $annotation->getName());
        $this->assertEquals('MyStruct', $annotation->getClass());
        $this->assertArrayHasKey('abc', $annotation->getConfig());
        $config = $annotation->getConfig();
        $this->assertEquals('xyz', $config['abc']);
    }

    public function testMissedClass()
    {
        $this->setExpectedException('\Doctrine\Common\Annotations\AnnotationException', 'Required property annotation is missed: class');
        new Struct(array('name' => 'test'));
    }

    public function testInlineStructureDefinition()
    {
        $properties = array(
            new Property(array('name' => 'test', 'type' => 'type')),
            new Boolean(array('name' => 'b')),
            new Struct(array('name' => 'test', 'class' => 'class')),
        );
        $annotation = new Struct(array(
            'name'  => 'test',
            'value' => $properties,
        ));
        $this->assertEquals($properties, $annotation->getProperties());
    }

}
