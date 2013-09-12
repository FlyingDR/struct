<?php

namespace Flying\Tests\Annotation;

use Flying\Struct\Annotation\Struct\Property;
use Flying\Tests\TestCase;

class PropertyTest extends TestCase
{
    protected $_propertyNamespace = 'Flying\Struct\Annotation\Struct';
    protected $_typesMap = array(
        'Boolean'    => 'boolean',
        'Int'        => 'int',
        'String'     => 'string',
        'Enum'       => 'enum',
        'Collection' => 'collection',
    );

    public function testInheritance()
    {
        $annotation = new Property(array('name' => 'test', 'type' => 'test'));
        $this->assertInstanceOf('Flying\Struct\Annotation\Struct\Annotation', $annotation);
    }

    public function testBasicOperations()
    {
        $annotation = new Property(array(
            'name' => 'test',
            'type' => 'boolean',
            'abc'  => 'xyz',
        ));
        $this->assertEquals('test', $annotation->getName());
        $this->assertEquals('boolean', $annotation->getType());
        $this->assertArrayHasKey('abc', $annotation->getConfig());
        $config = $annotation->getConfig();
        $this->assertEquals('xyz', $config['abc']);
    }

    public function testMissedName()
    {
        $this->setExpectedException('\Doctrine\Common\Annotations\AnnotationException', 'Required property annotation is missed: name');
        new Property(array());
    }

    public function testMissedType()
    {
        $this->setExpectedException('\Doctrine\Common\Annotations\AnnotationException', 'Required property annotation is missed: type');
        new Property(array(
            'name' => 'test',
        ));
    }

    public function testTypeAnnotations()
    {
        foreach ($this->_typesMap as $class => $type) {
            $class = trim($this->_propertyNamespace, '\\') . '\\' . $class;
            /** @var $annotation Property */
            $annotation = new $class(array(
                'name' => 'test',
            ));
            $this->assertInstanceOf(trim($this->_propertyNamespace, '\\') . '\\Property', $annotation);
            $this->assertEquals($type, $annotation->getType());
        }
    }

}
