<?php

namespace Flying\Tests\Configuration;

use Flying\Struct\Configuration\NamespacesMap;
use Flying\Tests\TestCase;

class NamespacesMapTest extends TestCase
{

    public function testBasicOperations()
    {
        $map = $this->getObject();
        $map->add('\\A\\B\\C\\', 'test');
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
        $map->add('A\\B\\C', 'test');
        $this->assertEquals($map->get('test'), 'A\B\C');
        $map->add('\\A\\B\\C', 'test');
        $this->assertEquals($map->get('test'), 'A\B\C');
        $map->add('A\\B\\C\\', 'test');
        $this->assertEquals($map->get('test'), 'A\B\C');
    }

    /**
     * @param mixed $ns
     * @param mixed $alias
     * @param array $expected
     * @dataProvider dataProviderRegisteringNamespaces
     */
    public function testRegisteringNamespaces($ns, $alias, $expected)
    {
        $map = $this->getObject();
        $map->add($ns, $alias);
        $this->assertEquals($expected, $map->getAll());
    }

    public function dataProviderRegisteringNamespaces()
    {
        return array(
            array(null, null, array()),
            array(null, 'test', array()),
            array('A\B\C', null, array('a_b_c' => 'A\B\C')),
            array('A\B\C', 'test', array('test' => 'A\B\C')),
            array(array('A\B\C', 'D\E\F'), null, array('a_b_c' => 'A\B\C', 'd_e_f' => 'D\E\F')),
            array(array('A\B\C', 'D\E\F'), 'test', array('a_b_c' => 'A\B\C', 'd_e_f' => 'D\E\F')),
            array(array('a' => 'A\B\C', 'b' => 'D\E\F'), null, array('a' => 'A\B\C', 'b' => 'D\E\F')),
        );
    }

    /**
     * @param mixed $ns
     * @param array $expected
     * @dataProvider dataProviderRegisteringNamespacesThroughConstructor
     */
    public function testRegisteringNamespacesThroughConstructor($ns, $expected)
    {
        $map = $this->getObject($ns);
        $this->assertEquals($expected, $map->getAll());
    }

    public function dataProviderRegisteringNamespacesThroughConstructor()
    {
        return array(
            array(null, array()),
            array('A\B\C', array('a_b_c' => 'A\B\C')),
            array(array('A\B\C', 'D\E\F'), array('a_b_c' => 'A\B\C', 'd_e_f' => 'D\E\F')),
            array(array('a' => 'A\B\C', 'b' => 'D\E\F'), array('a' => 'A\B\C', 'b' => 'D\E\F')),
        );
    }

    public function testGettingInvalidNamespace()
    {
        $map = $this->getObject();
        $this->setExpectedException('Flying\Struct\Exception');
        $map->get('unavailable');
    }

    /**
     * @dataProvider dataProviderSettingInvalidNamespace
     */
    public function testSettingInvalidNamespace($ns, $alias = null)
    {
        $map = $this->getObject();
        $this->setExpectedException('\InvalidArgumentException', 'Class namespace must be a string');
        $map->add($ns, $alias);
    }

    public function dataProviderSettingInvalidNamespace()
    {
        return array(
            array(true),
            array(12345),
            array(''),
            array(new \ArrayObject()),
        );
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
