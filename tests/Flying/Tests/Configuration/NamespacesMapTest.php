<?php

namespace Flying\Tests\Configuration;

use Flying\Struct\Configuration\NamespacesMap;
use Flying\Tests\TestCase;

class NamespacesMapTest extends TestCase
{

    public function testBasicOperations()
    {
        $map = $this->getObject();
        $map->add('test', '\\A\\B\\C\\');
        $this->assertTrue($map->has('test'));
        $this->assertFalse($map->has('unavailable'));
        $this->assertEquals($map->get('test'), 'A\B\C');
        $temp = $map->getAll();
        $this->assertEquals(sizeof($temp), 1);
        $this->assertArrayHasKey('test', $temp);
        $this->assertEquals($temp['test'], 'A\B\C');
        $map->remove('test');
        $this->assertFalse($map->has('test'));
        $this->assertEquals(sizeof($map->getAll()), 0);
    }

    public function testNamespaceSlashesTrimming()
    {
        $map = $this->getObject();
        $map->add('test', 'A\\B\\C');
        $this->assertEquals($map->get('test'), 'A\B\C');
        $map->add('test', '\\A\\B\\C');
        $this->assertEquals($map->get('test'), 'A\B\C');
        $map->add('test', 'A\\B\\C\\');
        $this->assertEquals($map->get('test'), 'A\B\C');
    }

    public function testRegisteringNamespacesThroughConstructor()
    {
        $map = $this->getObject();
        $this->assertEmpty($map->getAll());
        $map = $this->getObject('A\B\C');
        $this->assertEquals(array('a_b_c' => 'A\B\C'), $map->getAll());
        $map = $this->getObject(array('A\B\C', 'D\E\F'));
        $this->assertEquals(array('a_b_c' => 'A\B\C', 'd_e_f' => 'D\E\F'), $map->getAll());
        $map = $this->getObject(array('a' => 'A\B\C', 'b' => 'D\E\F'));
        $this->assertEquals(array('a' => 'A\B\C', 'b' => 'D\E\F'), $map->getAll());
    }

    public function testGettingInvalidNamespace()
    {
        $map = $this->getObject();
        $this->setExpectedException('Flying\Struct\Exception');
        $map->get('unavailable');
    }

    public function testSettingInvalidNamespaceAlias()
    {
        $map = $this->getObject();
        $this->setExpectedException('\InvalidArgumentException', 'Class namespace alias must be a string');
        $map->add(array('test'), null);
    }

    public function testSettingInvalidNamespace()
    {
        $map = $this->getObject();
        $this->setExpectedException('\InvalidArgumentException', 'Class namespace must be a string');
        $map->add('test', null);
    }

    /**
     * @param array $namespaces
     * @return NamespacesMap
     */
    protected function getObject($namespaces = null)
    {
        return new NamespacesMap($namespaces);
    }

}
