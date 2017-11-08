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
        static::assertTrue($map->has('test'));
        static::assertFalse($map->has('unavailable'));
        static::assertEquals($map->get('test'), 'A\B\C');
        $temp = $map->getAll();
        static::assertCount(1, $temp);
        static::assertArrayHasKey('test', $temp);
        static::assertEquals($temp['test'], 'A\B\C');
        $map->remove('test');
        static::assertFalse($map->has('test'));
        static::assertCount(0, $map->getAll());
    }

    /**
     * @param array $namespaces
     * @return NamespacesMap
     */
    protected function getObject($namespaces = null)
    {
        return new NamespacesMap($namespaces);
    }

    public function testNamespaceSlashesTrimming()
    {
        $map = $this->getObject();
        $map->add('A\\B\\C', 'test');
        static::assertEquals($map->get('test'), 'A\B\C');
        $map->add('\\A\\B\\C', 'test');
        static::assertEquals($map->get('test'), 'A\B\C');
        $map->add('A\\B\\C\\', 'test');
        static::assertEquals($map->get('test'), 'A\B\C');
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
        static::assertEquals($expected, $map->getAll());
    }

    public function dataProviderRegisteringNamespaces()
    {
        return [
            [null, null, []],
            [null, 'test', []],
            ['A\B\C', null, ['a_b_c' => 'A\B\C']],
            ['A\B\C', 'test', ['test' => 'A\B\C']],
            [['A\B\C', 'D\E\F'], null, ['a_b_c' => 'A\B\C', 'd_e_f' => 'D\E\F']],
            [['A\B\C', 'D\E\F'], 'test', ['a_b_c' => 'A\B\C', 'd_e_f' => 'D\E\F']],
            [['a' => 'A\B\C', 'b' => 'D\E\F'], null, ['a' => 'A\B\C', 'b' => 'D\E\F']],
        ];
    }

    /**
     * @param mixed $ns
     * @param array $expected
     * @dataProvider dataProviderRegisteringNamespacesThroughConstructor
     */
    public function testRegisteringNamespacesThroughConstructor($ns, $expected)
    {
        $map = $this->getObject($ns);
        static::assertEquals($expected, $map->getAll());
    }

    public function dataProviderRegisteringNamespacesThroughConstructor()
    {
        return [
            [null, []],
            ['A\B\C', ['a_b_c' => 'A\B\C']],
            [['A\B\C', 'D\E\F'], ['a_b_c' => 'A\B\C', 'd_e_f' => 'D\E\F']],
            [['a' => 'A\B\C', 'b' => 'D\E\F'], ['a' => 'A\B\C', 'b' => 'D\E\F']],
        ];
    }

    /**
     * @expectedException \Flying\Struct\Exception
     */
    public function testGettingInvalidNamespace()
    {
        $map = $this->getObject();
        $map->get('unavailable');
    }

    /**
     * @dataProvider             dataProviderSettingInvalidNamespace
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Class namespace must be a string
     * @param string|array $ns
     * @param string|null $alias
     */
    public function testSettingInvalidNamespace($ns, $alias = null)
    {
        $map = $this->getObject();
        $map->add($ns, $alias);
    }

    public function dataProviderSettingInvalidNamespace()
    {
        return [
            [true],
            [12345],
            [''],
            [new \ArrayObject()],
        ];
    }
}
