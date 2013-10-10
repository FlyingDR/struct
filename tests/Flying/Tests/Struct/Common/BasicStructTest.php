<?php

namespace Flying\Tests\Struct\Common;

use Flying\Struct\Configuration;
use Flying\Struct\Metadata\PropertyMetadata;
use Flying\Struct\Metadata\StructMetadata;
use Flying\Struct\Struct;
use Flying\Tests\Struct\Fixtures\BasicStruct;
use Flying\Tests\Tools\CallbackLog;
use Mockery;

/**
 * @method \Flying\Tests\Struct\Fixtures\BasicStruct getTestStruct($contents = null, $config = null)
 */
abstract class BasicStructTest extends BaseStructTest
{
    /**
     * Name of fixture class to test
     *
     * @var string
     */
    protected $fixtureClass = 'Flying\Tests\Struct\Fixtures\BasicStruct';

    public function testStructureInterfaces()
    {
        $reflection = new \ReflectionClass('Flying\Struct\Struct');
        $interfaces = $reflection->getInterfaces();
        $this->assertArrayHasKey('Flying\Struct\StructInterface', $interfaces);
        $this->assertArrayHasKey('Countable', $interfaces);
        $this->assertArrayHasKey('Iterator', $interfaces);
        $this->assertArrayHasKey('RecursiveIterator', $interfaces);
        $this->assertArrayHasKey('ArrayAccess', $interfaces);
        $this->assertArrayHasKey('Serializable', $interfaces);
        $this->assertArrayHasKey('Flying\Struct\Common\ComplexPropertyInterface', $interfaces);
        $this->assertArrayHasKey('Flying\Struct\Common\UpdateNotifyListenerInterface', $interfaces);
    }

    public function testExplicitGetter()
    {
        $struct = $this->getTestStruct();

        $this->assertTrue($struct->get('first'));
        $this->assertEquals(100, $struct->get('second'));
        $this->assertEquals('default', $struct->get('unknown', 'default'));
    }

    public function testGettingProperty()
    {
        $struct = $this->getTestStruct();
        $this->assertInstanceOf('Flying\Struct\Property\PropertyInterface', $struct->getProperty('first'));
        $this->assertNull($struct->getProperty('unavailable'));
    }

    public function testIssetUnset()
    {
        $struct = $this->getTestStruct();
        $this->assertTrue(isset($struct['first']));
        $this->assertFalse(isset($struct['unavailable']));

        // Unset of structure property should revert its value back to default value
        $this->assertEquals('some value', $struct->fourth);
        $struct->fourth = 'another value';
        $this->assertEquals('another value', $struct->fourth);
        unset($struct->fourth);
        $this->assertEquals('some value', $struct->fourth);
    }

    public function testIssetUnsetUsingArrayAccess()
    {
        $struct = $this->getTestStruct();
        // Unset of structure property should revert its value back to default value
        $this->assertEquals('some value', $struct['fourth']);
        $struct['fourth'] = 'another value';
        $this->assertEquals('another value', $struct['fourth']);
        unset($struct['fourth']);
        $this->assertEquals('some value', $struct['fourth']);
    }

    public function testSettingSingleValues()
    {
        $struct = $this->getTestStruct();

        $this->assertTrue($struct->first);
        $struct->first = null;
        $this->assertNull($struct->first);
        $struct->first = false;
        $this->assertFalse($struct->first);
        $struct->first = 'abc';
        $this->assertTrue($struct->first);

        $struct->second = null;
        $this->assertEquals(100, $struct->second);
        $struct->second = 12345;
        $this->assertEquals(1000, $struct->second);
        $struct->second = -123.45;
        $this->assertEquals(10, $struct->second);
        $struct->second = 'abc';
        $this->assertEquals(10, $struct->second);

        $struct->third = null;
        $this->assertNull($struct->third);
        $struct->third = true;
        $this->assertEquals('1', $struct->third);
        $struct->third = 12345;
        $this->assertEquals('12345', $struct->third);
        $struct->third = 'abc';
        $this->assertEquals('abc', $struct->third);
    }

    public function testSettingSingleValuesUsingArrayAccess()
    {
        $struct = $this->getTestStruct();

        $this->assertTrue($struct['first']);
        $struct['first'] = null;
        $this->assertNull($struct['first']);
        $struct['first'] = false;
        $this->assertFalse($struct['first']);
        $struct['first'] = 'abc';
        $this->assertTrue($struct['first']);

        $struct['second'] = null;
        $this->assertEquals(100, $struct['second']);
        $struct['second'] = 12345;
        $this->assertEquals(1000, $struct['second']);
        $struct['second'] = -123.45;
        $this->assertEquals(10, $struct['second']);
        $struct['second'] = 'abc';
        $this->assertEquals(10, $struct['second']);

        $struct['third'] = null;
        $this->assertNull($struct['third']);
        $struct['third'] = true;
        $this->assertEquals('1', $struct['third']);
        $struct['third'] = 12345;
        $this->assertEquals('12345', $struct['third']);
        $struct['third'] = 'abc';
        $this->assertEquals('abc', $struct['third']);
    }

    public function testSettingMultipleValues()
    {
        $struct = $this->getTestStruct();
        $modifications = array(
            'fourth'  => 'test',
            'first'   => false,
            'third'   => 'something',
            'second'  => 123,
            'unknown' => 'value',
        );
        $struct->set($modifications);
        $this->assertEquals(
            array(
                 'first'  => false,
                 'second' => 123,
                 'third'  => 'something',
                 'fourth' => 'test',
            ),
            $struct->toArray()
        );
    }

    public function testStructureReset()
    {
        $struct = $this->getTestStruct();
        $modifications = array(
            'fourth'  => 'test',
            'first'   => false,
            'third'   => 'something',
            'second'  => 123,
            'unknown' => 'value',
        );
        $struct->set($modifications);
        $this->assertEquals(
            array(
                 'first'  => false,
                 'second' => 123,
                 'third'  => 'something',
                 'fourth' => 'test',
            ),
            $struct->toArray()
        );
        $struct->reset();
        $this->assertEquals($struct->getExpectedContents(), $struct->toArray());
    }

    /**
     * @dataProvider getExplicitInitialContents
     */
    public function testExplicitlyGivenInitialContents($initial, $expected)
    {
        $class = $this->getFixtureClass('BasicStruct');
        /** @var $struct BasicStruct */
        $struct = new $class($initial);
        $this->assertEquals($expected, $struct->toArray());
    }

    public function getExplicitInitialContents()
    {
        $struct = new BasicStruct();
        $defaultExpected = $struct->getExpectedContents();
        $initial = array(
            'third'  => 'test value',
            'first'  => false,
            'second' => 123.45,
        );
        $expected = array(
            'first'  => false,
            'second' => 123,
            'third'  => 'test value',
            'fourth' => 'some value',
        );
        $tests = array();
        $tests[] = array($initial, $expected);
        $tests[] = array(true, $defaultExpected);
        $tests[] = array('some test value', $defaultExpected);
        $tests[] = array(array(), $defaultExpected);
        $tests[] = array(new \SplFixedArray(), $defaultExpected);
        $tests[] = array(new \ArrayObject(), $defaultExpected);
        $tests[] = array(new \ArrayObject($initial), $expected);
        $tests[] = array(new \ArrayIterator($initial), $expected);
        $struct->set($initial);
        $tests[] = array($struct, $expected);
        return $tests;
    }

    public function testUpdateNotification()
    {
        $mock = Mockery::mock('Flying\Struct\Common\UpdateNotifyListenerInterface')
            ->shouldReceive('updateNotify')->once()
            ->with(Mockery::type('Flying\Struct\Common\SimplePropertyInterface'))
            ->getMock();
        $struct = $this->getTestStruct(
            null,
            array(
                 'update_notify_listener' => $mock,
            )
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
        $this->assertEquals($value, $struct->get($name, $value));
        $log = $logger->get();
        $this->assertEquals(1, sizeof($log));
        $log = array_shift($log);
        $this->assertEquals(array($method, $name, $value), $log);
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
        $this->assertEquals(1, sizeof($log));
        $log = array_shift($log);
        $this->assertEquals(array($method, $name, $value), $log);
    }

    public function testOnChangeCallback()
    {
        $struct = $this->getTestStruct();
        $logger = new CallbackLog();
        $method = 'onChange';
        $changes = array(
            'second'      => 123.45,
            'unavailable' => 'test',
            'first'       => true,
        );
        $struct->setCallbackLogger($method, $logger);
        $struct->set($changes);
        unset($changes['unavailable']);
        $log = $logger->get();
        $this->assertEquals(sizeof($changes), sizeof($log));
        foreach ($changes as $name => $value) {
            $temp = array_shift($log);
            $this->assertEquals(array($method, $name), $temp);
        }
    }

    public function testExplicitMetadata()
    {
        $metadata = new StructMetadata();
        $metadata->addProperty(new PropertyMetadata('a', 'Flying\Struct\Property\Int', array('default' => 123)));
        $metadata->addProperty(new PropertyMetadata('b', 'Flying\Struct\Property\Boolean', array('default' => false)));
        $metadata->addProperty(new PropertyMetadata('c', 'Flying\Struct\Property\String', array('default' => 'test')));
        $struct = new Struct(null, array(
                                        'metadata' => $metadata,
                                   ));
        $this->assertEquals(
            array(
                 'a' => 123,
                 'b' => false,
                 'c' => 'test',
            ),
            $struct->toArray()
        );
    }

    public function testCloning()
    {
        $struct = $this->getTestStruct();
        $clone = clone $struct;
        foreach ($struct as $name => $value) {
            $this->assertEquals($value, $clone->get($name));
        }
        $clone->set(
            array(
                 'first'  => false,
                 'second' => 77,
                 'third'  => 'modified',
                 'fourth' => 'another value',
            )
        );
        foreach ($struct as $name => $value) {
            $this->assertNotEquals($value, $clone->get($name));
        }
    }
}
