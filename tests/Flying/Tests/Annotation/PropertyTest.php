<?php

namespace Flying\Tests\Annotation;

use Flying\Struct\Annotation\Struct\Property;
use Flying\Tests\TestCase;

class PropertyTest extends TestCase
{
    protected $propertyNamespace = 'Flying\Struct\Annotation\Struct';
    protected static $typesMap = array(
        'Boolean'    => 'boolean',
        'Int'        => 'int',
        'String'     => 'string',
        'Enum'       => 'enum',
        'Collection' => 'collection',
    );

    public function testInheritance()
    {
        $annotation = new Property(array('name' => 'test', 'type' => 'test'));
        static::assertInstanceOf('Flying\Struct\Annotation\Struct\Annotation', $annotation);
    }

    public function testBasicOperations()
    {
        $annotation = new Property(array(
            'name' => 'test',
            'type' => 'boolean',
            'abc'  => 'xyz',
        ));
        static::assertEquals('test', $annotation->getName());
        static::assertEquals('boolean', $annotation->getType());
        static::assertArrayHasKey('abc', $annotation->getConfig());
        $config = $annotation->getConfig();
        static::assertEquals('xyz', $config['abc']);
    }

    public function testMissedName()
    {
        $this->setExpectedException(
            'Doctrine\Common\Annotations\AnnotationException',
            'Required property annotation is missed: name'
        );
        new Property(array());
    }

    public function testMissedType()
    {
        $this->setExpectedException(
            'Doctrine\Common\Annotations\AnnotationException',
            'Required property annotation is missed: type'
        );
        new Property(array(
            'name' => 'test',
        ));
    }

    public function testTypeAnnotations()
    {
        foreach (self::$typesMap as $class => $type) {
            $class = trim($this->propertyNamespace, '\\') . '\\' . $class;
            /** @var $annotation Property */
            $annotation = new $class(array(
                'name' => 'test',
            ));
            static::assertInstanceOf(trim($this->propertyNamespace, '\\') . '\\Property', $annotation);
            static::assertEquals($type, $annotation->getType());
        }
    }
}
