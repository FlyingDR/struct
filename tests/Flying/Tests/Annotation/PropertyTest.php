<?php

namespace Flying\Tests\Annotation;

use Flying\Struct\Annotation\Struct\Property;

class PropertyTest extends \PHPUnit_Framework_TestCase
{
    protected $_propertyNamespace = 'Flying\Struct\Annotation\Struct';
    protected $_typesMap = array(
        'Boolean' => 'boolean',
        'Int'     => 'int',
        'String'  => 'string',
    );

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
        $annotation = new Property(array());
    }

    public function testMissedType()
    {
        $this->setExpectedException('\Doctrine\Common\Annotations\AnnotationException', 'Required property annotation is missed: type');
        $annotation = new Property(array(
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
