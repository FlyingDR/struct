<?php

namespace Flying\Tests\Annotation;

use Flying\Struct\Annotation\Struct\Struct;
use Flying\Tests\TestCase;

class StructTest extends TestCase
{

    public function testInheritance()
    {
        $annotation = new Struct(array('name' => 'test', 'class' => 'test'));
        $this->assertInstanceOf('Flying\Struct\Annotation\Struct\Annotation', $annotation);
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
        $this->setExpectedException('\Doctrine\Common\Annotations\AnnotationException', 'Required structure annotation is missed: class');
        new Struct(array('name' => 'test'));
    }

}
