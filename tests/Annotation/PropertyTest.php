<?php

namespace Flying\Tests\Annotation;

use Flying\Struct\Annotation\Annotation;
use Flying\Struct\Annotation\Property;
use Flying\Tests\TestCase;

class PropertyTest extends TestCase
{
    protected static $typesMap = [
        'Boolean'    => 'boolean',
        'Integer'    => 'integer',
        'Str'        => 'str',
        'Enum'       => 'enum',
        'Collection' => 'collection',
    ];
    protected $propertyNamespace = 'Flying\Struct\Annotation';

    public function testInheritance()
    {
        $annotation = new Property(['name' => 'test', 'type' => 'test']);
        static::assertInstanceOf(Annotation::class, $annotation);
    }

    public function testBasicOperations()
    {
        $annotation = new Property([
            'name' => 'test',
            'type' => 'boolean',
            'abc'  => 'xyz',
        ]);
        static::assertEquals('test', $annotation->getName());
        static::assertEquals('boolean', $annotation->getType());
        static::assertArrayHasKey('abc', $annotation->getConfig());
        $config = $annotation->getConfig();
        static::assertEquals('xyz', $config['abc']);
    }

    /**
     * @expectedException \Doctrine\Common\Annotations\AnnotationException
     * @expectedExceptionMessage Required property annotation is missed: name
     */
    public function testMissedName()
    {
        new Property([]);
    }

    /**
     * @expectedException \Doctrine\Common\Annotations\AnnotationException
     * @expectedExceptionMessage Required property annotation is missed: type
     */
    public function testMissedType()
    {
        new Property([
            'name' => 'test',
        ]);
    }

    public function testTypeAnnotations()
    {
        foreach (self::$typesMap as $class => $type) {
            $class = trim($this->propertyNamespace, '\\') . '\\' . $class;
            /** @var $annotation Property */
            $annotation = new $class([
                'name' => 'test',
            ]);
            static::assertInstanceOf(trim($this->propertyNamespace, '\\') . '\\Property', $annotation);
            static::assertEquals($type, $annotation->getType());
        }
    }
}
