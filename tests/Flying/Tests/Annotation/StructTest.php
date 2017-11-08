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
        $annotation = new Struct(['name' => 'test', 'class' => 'test']);
        static::assertInstanceOf('Flying\Struct\Annotation\Struct\Annotation', $annotation);
    }

    public function testBasicOperations()
    {
        $annotation = new Struct([
            'name'  => 'test',
            'class' => 'MyStruct',
            'abc'   => 'xyz',
        ]);
        static::assertEquals('test', $annotation->getName());
        static::assertEquals('MyStruct', $annotation->getClass());
        static::assertArrayHasKey('abc', $annotation->getConfig());
        $config = $annotation->getConfig();
        static::assertEquals('xyz', $config['abc']);
    }

    public function testMissedClass()
    {
        $this->setExpectedException(
            'Doctrine\Common\Annotations\AnnotationException',
            'Required property annotation is missed: class'
        );
        new Struct(['name' => 'test']);
    }

    public function testInlineStructureDefinition()
    {
        $properties = [
            new Property(['name' => 'test', 'type' => 'type']),
            new Boolean(['name' => 'b']),
            new Struct(['name' => 'test', 'class' => 'class']),
        ];
        $annotation = new Struct([
            'name'  => 'test',
            'value' => $properties,
        ]);
        static::assertEquals($properties, $annotation->getProperties());
    }
}
