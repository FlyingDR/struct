<?php

namespace Flying\Tests\Struct\Common;

use Flying\Struct\Common\ComplexPropertyInterface;
use Flying\Tests\Struct\Fixtures\MultiLevelStruct;
use Mockery;

/**
 * @method MultiLevelStruct getTestStruct($contents = null, $config = null)
 */
abstract class MultiLevelStructTest extends BaseStructTest
{
    /**
     * Name of fixture class to test
     *
     * @var string
     */
    protected $fixtureClass = 'Flying\Tests\Struct\Fixtures\MultiLevelStruct';

    public function testCreation()
    {
        $struct = $this->getTestStruct();
        static::assertEquals($struct->getExpectedContents(), $struct->toArray());
    }

    public function testGettingProperty()
    {
        $struct = $this->getTestStruct();
        static::assertInstanceOf('Flying\Struct\Property\PropertyInterface', $struct->getProperty('b'));
        static::assertInstanceOf('Flying\Struct\StructInterface', $struct->getProperty('child'));
        static::assertInstanceOf('Flying\Struct\Property\PropertyInterface', $struct->child->getProperty('x'));
        static::assertNull($struct->getProperty('unavailable'));
    }

    public function testGettingChildStructureProperty()
    {
        $struct = $this->getTestStruct();
        static::assertFalse($struct->child->x);
        static::assertEquals(345, $struct->child->y);
        static::assertEquals('string', $struct->child->z);
    }

    public function testSettingSingleChildStructureProperty()
    {
        $struct = $this->getTestStruct();
        $struct->child->x = true;
        $struct->child->y = 777;
        $struct->child->z = 'test string';
        static::assertTrue($struct->child->x);
        static::assertEquals(777, $struct->child->y);
        static::assertEquals('test string', $struct->child->z);
    }

    public function testSettingMultipleChildStructureProperties()
    {
        $struct = $this->getTestStruct();
        $struct->child->set([
            'x' => true,
            'y' => 777,
            'z' => 'test string',
        ]);
        static::assertTrue($struct->child->x);
        static::assertEquals(777, $struct->child->y);
        static::assertEquals('test string', $struct->child->z);

        $struct = $this->getTestStruct();
        $struct->child = [
            'x' => true,
            'y' => 777,
            'z' => 'test string',
        ];
        /** @noinspection PhpUndefinedFieldInspection */
        static::assertTrue($struct->child->x);
        /** @noinspection PhpUndefinedFieldInspection */
        static::assertEquals(777, $struct->child->y);
        /** @noinspection PhpUndefinedFieldInspection */
        static::assertEquals('test string', $struct->child->z);
    }

    public function testUpdateNotificationBubbling()
    {
        $struct = $this->getTestStruct();
        $m1 = Mockery::mock('Flying\Struct\Common\UpdateNotifyListenerInterface')
            ->shouldReceive('updateNotify')->once()
            ->with(Mockery::type('Flying\Struct\Common\SimplePropertyInterface'))
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
                    static::assertEquals($v, $cloned->get($k));
                }
            } else {
                static::assertEquals($value, $clone->get($name));
            }
        }
        $clone->set([
            'b'     => false,
            'i'     => 345,
            's'     => 'changed',
            'child' => [
                'x' => true,
                'y' => 777,
                'z' => 'modified',
            ],
        ]);
        foreach ($struct as $name => $value) {
            if ($value instanceof ComplexPropertyInterface) {
                $cloned = $clone->get($name);
                foreach ($value as $k => $v) {
                    static::assertNotEquals($v, $cloned->get($k));
                }
            } else {
                static::assertNotEquals($value, $clone->get($name));
            }
        }
    }
}
