<?php

namespace Flying\Tests\Struct\Common;

use Flying\Struct\Common\ComplexPropertyInterface;
use Flying\Tests\Struct\Fixtures\MultiLevelStruct;
use Mockery;

abstract class MultiLevelStructTest extends BaseStructTest
{

    public function testCreation()
    {
        $struct = $this->getTestStruct();
        $this->assertEquals($struct->getExpectedContents(), $struct->toArray());
    }

    public function testRecursiveIteratorInterface()
    {
        $struct = $this->getTestStruct();
        $iterator = new \RecursiveIteratorIterator($struct);
        $contents = array();
        foreach ($iterator as $key => $value) {
            $contents[$key] = $value;
        }
        $expected = $struct->getExpectedContents();
        $expected = array_merge($expected, $expected['child']);
        unset($expected['child']);
        $this->assertEquals($expected, $contents);
    }

    public function testGettingChildStructureProperty()
    {
        $struct = $this->getTestStruct();
        $this->assertFalse($struct->child->x);
        $this->assertEquals(345, $struct->child->y);
        $this->assertEquals('string', $struct->child->z);
    }

    public function testSettingSingleChildStructureProperty()
    {
        $struct = $this->getTestStruct();
        $struct->child->x = true;
        $struct->child->y = 777;
        $struct->child->z = 'test string';
        $this->assertTrue($struct->child->x);
        $this->assertEquals(777, $struct->child->y);
        $this->assertEquals('test string', $struct->child->z);
    }

    public function testSettingMultipleChildStructureProperties()
    {
        $struct = $this->getTestStruct();
        $struct->child->set(array(
            'x' => true,
            'y' => 777,
            'z' => 'test string',
        ));
        $this->assertTrue($struct->child->x);
        $this->assertEquals(777, $struct->child->y);
        $this->assertEquals('test string', $struct->child->z);

        $struct = $this->getTestStruct();
        $struct->child = array(
            'x' => true,
            'y' => 777,
            'z' => 'test string',
        );
        /** @noinspection PhpUndefinedFieldInspection */
        $this->assertTrue($struct->child->x);
        /** @noinspection PhpUndefinedFieldInspection */
        $this->assertEquals(777, $struct->child->y);
        /** @noinspection PhpUndefinedFieldInspection */
        $this->assertEquals('test string', $struct->child->z);
    }

    public function testUpdateNotificationBubbling()
    {
        $struct = $this->getTestStruct();
        $m1 = Mockery::mock('Flying\Struct\Common\UpdateNotifyListenerInterface')
            ->shouldReceive('updateNotify')->once()
            ->with(Mockery::type('\Flying\Struct\Common\SimplePropertyInterface'))
            ->getMock();
        $struct->setConfig('update_notify_listener', $m1);
        $m2 = clone($m1);
        $struct->child->setConfig('update_notify_listener', $m2);
        $struct->child->x = true;
    }

    public function testCloning()
    {
        $struct = $this->getTestStruct();
        $clone = clone $struct;
        foreach ($struct as $name => $value) {
            if ($value instanceof ComplexPropertyInterface) {
                $cloned = $clone->get($name);
                foreach ($value as $k => $v) {
                    $this->assertEquals($v, $cloned->get($k));
                }
            } else {
                $this->assertEquals($value, $clone->get($name));
            }
        }
        $clone->set(array(
            'b'     => false,
            'i'     => 345,
            's'     => 'changed',
            'child' => array(
                'x' => true,
                'y' => 777,
                'z' => 'modified',
            ),
        ));
        foreach ($struct as $name => $value) {
            if ($value instanceof ComplexPropertyInterface) {
                $cloned = $clone->get($name);
                foreach ($value as $k => $v) {
                    $this->assertNotEquals($v, $cloned->get($k));
                }
            } else {
                $this->assertNotEquals($value, $clone->get($name));
            }
        }
    }

    /**
     * @param array|object $contents    OPTIONAL Contents to initialize structure with
     * @param array|object $config      OPTIONAL Configuration for this structure
     * @return MultiLevelStruct
     */
    protected function getTestStruct($contents = null, $config = null)
    {
        $class = $this->getFixtureClass('MultiLevelStruct');
        $struct = new $class($contents, $config);
        return $struct;
    }

}
