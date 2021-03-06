<?php

namespace Flying\Tests\Struct\Common;

use Flying\Struct\Common\ComplexPropertyInterface;
use Flying\Struct\Common\SimplePropertyInterface;
use Flying\Struct\Common\UpdateNotifyListenerInterface;
use Flying\Struct\Metadata\PropertyMetadata;
use Flying\Struct\Metadata\StructMetadata;
use Flying\Struct\Property\Boolean;
use Flying\Struct\Property\Integer;
use Flying\Struct\Property\PropertyInterface;
use Flying\Struct\Property\Str;
use Flying\Struct\Struct;
use Flying\Struct\StructInterface;
use Flying\Tests\Struct\Fixtures\BasicStruct;
use Flying\Tests\Tools\CallbackLog;
use Mockery;

/**
 * @method BasicStruct getTestStruct($contents = null, $config = null)
 */
abstract class BasicStructTest extends BaseStructTest
{
    /**
     * Name of fixture class to test
     *
     * @var string
     */
    protected $fixtureClass = BasicStruct::class;

    public function testStructureInterfaces()
    {
        $reflection = new \ReflectionClass(Struct::class);
        $interfaces = $reflection->getInterfaces();
        static::assertArrayHasKey(StructInterface::class, $interfaces);
        static::assertArrayHasKey('Countable', $interfaces);
        static::assertArrayHasKey('Iterator', $interfaces);
        static::assertArrayHasKey('RecursiveIterator', $interfaces);
        static::assertArrayHasKey('ArrayAccess', $interfaces);
        static::assertArrayHasKey('Serializable', $interfaces);
        static::assertArrayHasKey(ComplexPropertyInterface::class, $interfaces);
        static::assertArrayHasKey(UpdateNotifyListenerInterface::class, $interfaces);
    }

    public function testExplicitGetter()
    {
        $struct = $this->getTestStruct();

        static::assertTrue($struct->get('first'));
        static::assertEquals(100, $struct->get('second'));
        static::assertEquals('default', $struct->get('unknown', 'default'));
    }

    public function testGettingProperty()
    {
        $struct = $this->getTestStruct();
        static::assertInstanceOf(PropertyInterface::class, $struct->getProperty('first'));
        static::assertNull($struct->getProperty('unavailable'));
    }

    public function testIssetUnset()
    {
        $struct = $this->getTestStruct();
        static::assertTrue(isset($struct['first']));
        static::assertFalse(isset($struct['unavailable']));

        // Unset of structure property should revert its value back to default value
        static::assertEquals('some value', $struct->fourth);
        $struct->fourth = 'another value';
        static::assertEquals('another value', $struct->fourth);
        unset($struct->fourth);
        static::assertEquals('some value', $struct->fourth);
    }

    public function testIssetUnsetUsingArrayAccess()
    {
        $struct = $this->getTestStruct();
        // Unset of structure property should revert its value back to default value
        static::assertEquals('some value', $struct['fourth']);
        $struct['fourth'] = 'another value';
        static::assertEquals('another value', $struct['fourth']);
        unset($struct['fourth']);
        static::assertEquals('some value', $struct['fourth']);
    }

    public function testSettingSingleValues()
    {
        $struct = $this->getTestStruct();

        static::assertTrue($struct->first);
        $struct->first = null;
        static::assertNull($struct->first);
        $struct->first = false;
        static::assertFalse($struct->first);
        $struct->first = 'abc';
        static::assertTrue($struct->first);

        $struct->second = null;
        static::assertEquals(100, $struct->second);
        $struct->second = 12345;
        static::assertEquals(1000, $struct->second);
        $struct->second = -123.45;
        static::assertEquals(10, $struct->second);
        $struct->second = 'abc';
        static::assertEquals(10, $struct->second);

        $struct->third = null;
        static::assertNull($struct->third);
        $struct->third = true;
        static::assertEquals('1', $struct->third);
        $struct->third = 12345;
        static::assertEquals('12345', $struct->third);
        $struct->third = 'abc';
        static::assertEquals('abc', $struct->third);
    }

    public function testSettingSingleValuesUsingArrayAccess()
    {
        $struct = $this->getTestStruct();

        static::assertTrue($struct['first']);
        $struct['first'] = null;
        static::assertNull($struct['first']);
        $struct['first'] = false;
        static::assertFalse($struct['first']);
        $struct['first'] = 'abc';
        static::assertTrue($struct['first']);

        $struct['second'] = null;
        static::assertEquals(100, $struct['second']);
        $struct['second'] = 12345;
        static::assertEquals(1000, $struct['second']);
        $struct['second'] = -123.45;
        static::assertEquals(10, $struct['second']);
        $struct['second'] = 'abc';
        static::assertEquals(10, $struct['second']);

        $struct['third'] = null;
        static::assertNull($struct['third']);
        $struct['third'] = true;
        static::assertEquals('1', $struct['third']);
        $struct['third'] = 12345;
        static::assertEquals('12345', $struct['third']);
        $struct['third'] = 'abc';
        static::assertEquals('abc', $struct['third']);
    }

    public function testSettingMultipleValues()
    {
        $struct = $this->getTestStruct();
        $modifications = [
            'fourth'  => 'test',
            'first'   => false,
            'third'   => 'something',
            'second'  => 123,
            'unknown' => 'value',
        ];
        $struct->set($modifications);
        static::assertEquals([
            'first'  => false,
            'second' => 123,
            'third'  => 'something',
            'fourth' => 'test',
        ], $struct->toArray());
    }

    public function testStructureReset()
    {
        $struct = $this->getTestStruct();
        $modifications = [
            'fourth'  => 'test',
            'first'   => false,
            'third'   => 'something',
            'second'  => 123,
            'unknown' => 'value',
        ];
        $struct->set($modifications);
        static::assertEquals([
            'first'  => false,
            'second' => 123,
            'third'  => 'something',
            'fourth' => 'test',
        ], $struct->toArray());
        $struct->reset();
        static::assertEquals($struct->getExpectedContents(), $struct->toArray());
    }

    /**
     * @dataProvider getExplicitInitialContents
     * @param $initial
     * @param $expected
     */
    public function testExplicitlyGivenInitialContents($initial, $expected)
    {
        $class = $this->getFixtureClass('BasicStruct');
        /** @var $struct BasicStruct */
        $struct = new $class($initial);
        static::assertEquals($expected, $struct->toArray());
    }

    public function getExplicitInitialContents()
    {
        $struct = new BasicStruct();
        $defaultExpected = $struct->getExpectedContents();
        $initial = [
            'third'  => 'test value',
            'first'  => false,
            'second' => 123.45,
        ];
        $expected = [
            'first'  => false,
            'second' => 123,
            'third'  => 'test value',
            'fourth' => 'some value',
        ];
        $tests = [];
        $tests[] = [$initial, $expected];
        $tests[] = [true, $defaultExpected];
        $tests[] = ['some test value', $defaultExpected];
        $tests[] = [[], $defaultExpected];
        $tests[] = [new \SplFixedArray(), $defaultExpected];
        $tests[] = [new \ArrayObject(), $defaultExpected];
        $tests[] = [new \ArrayObject($initial), $expected];
        $tests[] = [new \ArrayIterator($initial), $expected];
        $struct->set($initial);
        $tests[] = [$struct, $expected];
        return $tests;
    }

    public function testUpdateNotification()
    {
        $mock = Mockery::mock(UpdateNotifyListenerInterface::class)
            ->shouldReceive('updateNotify')->once()
            ->with(Mockery::type(SimplePropertyInterface::class))
            ->getMock();
        $struct = $this->getTestStruct(
            null,
            ['update_notify_listener' => $mock]
        );
        $struct->set('first', true);
        $struct->set('unavailable', false);
    }

    public function testGetMissedCallback()
    {
        $struct = $this->getTestStruct();
        $logger = new CallbackLog();
        $method = 'getMissed';
        $name = 'unavailable';
        $value = 'test';
        $struct->setCallbackLogger($method, $logger);
        static::assertEquals($value, $struct->get($name, $value));
        $log = $logger->get();
        static::assertCount(1, $log);
        $log = array_shift($log);
        static::assertEquals([$method, $name, $value], $log);
    }

    public function testSetMissedCallback()
    {
        $struct = $this->getTestStruct();
        $logger = new CallbackLog();
        $method = 'setMissed';
        $name = 'unavailable';
        $value = 'test';
        $struct->setCallbackLogger($method, $logger);
        $struct->set($name, $value);
        $log = $logger->get();
        static::assertCount(1, $log);
        $log = array_shift($log);
        static::assertEquals([$method, $name, $value], $log);
    }

    public function testOnChangeCallback()
    {
        $struct = $this->getTestStruct();
        $logger = new CallbackLog();
        $method = 'onChange';
        $changes = [
            'second'      => 123.45,
            'unavailable' => 'test',
            'first'       => true,
        ];
        $struct->setCallbackLogger($method, $logger);
        $struct->set($changes);
        unset($changes['unavailable']);
        $log = $logger->get();
        static::assertEquals(count($changes), count($log));
        foreach ($changes as $name => $value) {
            $temp = array_shift($log);
            static::assertEquals([$method, $name], $temp);
        }
    }

    public function testExplicitMetadata()
    {
        $metadata = new StructMetadata();
        $metadata->addProperty(new PropertyMetadata('a', Integer::class, ['default' => 123]));
        $metadata->addProperty(new PropertyMetadata('b', Boolean::class, ['default' => false]));
        $metadata->addProperty(new PropertyMetadata('c', Str::class, ['default' => 'test']));
        $struct = new Struct(null, [
            'metadata' => $metadata,
        ]);
        static::assertEquals([
            'a' => 123,
            'b' => false,
            'c' => 'test',
        ], $struct->toArray());
    }

    public function testCloning()
    {
        $struct = $this->getTestStruct();
        $clone = clone $struct;
        foreach ($struct as $name => $value) {
            static::assertEquals($value, $clone->get($name));
        }
        $clone->set([
            'first'  => false,
            'second' => 77,
            'third'  => 'modified',
            'fourth' => 'another value',
        ]);
        foreach ($struct as $name => $value) {
            static::assertNotEquals($value, $clone->get($name));
        }
    }
}
