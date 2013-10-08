<?php

namespace Flying\Tests\Property;

use Flying\Tests\Property\Fixtures\Collection;
use Flying\Tests\Property\Fixtures\CollectionWithCustomValidator;
use Flying\Tests\Property\Stubs\ToArray;
use Flying\Tests\Struct\Fixtures\BasicStruct;
use Flying\Tests\TestCase;
use Flying\Tests\Tools\CallbackLog;

class CollectionTest extends TestCase
{
    /**
     * @param array $value
     * @param int   $expected
     * @param array $config
     *
     * @dataProvider dataProviderCountableInterface
     */
    public function testCountableInterface($value, $expected, $config = null)
    {
        $collection = new Collection($value, $config);
        $this->assertEquals($expected, $collection->count());
        $this->assertEquals($expected, sizeof($collection));
    }

    public function dataProviderCountableInterface()
    {
        return array(
            array(null, 0),
            array(array(), 0),
            array(array(123), 1),
            array(array(1, 2, 3), 3),
            array(null, 3, array('default' => array(1, 2, 3)))
        );
    }

    public function testArrayAccess()
    {
        $collection = new Collection();
        $collection['a'] = 'b';
        $this->assertEquals(1, sizeof($collection));
        $this->assertTrue(isset($collection['a']));
        $this->assertEquals('b', $collection['a']);
        unset($collection['a']);
        $this->assertEquals(0, sizeof($collection));
        $this->assertFalse(isset($collection['a']));
        $collection[] = 'abc';
        $this->assertEquals(1, sizeof($collection));
        /** @var $collection Collection */
        $this->assertEquals(array('abc'), $collection->toArray());
    }

    public function testCollectionIterator()
    {
        $values = array('a' => 'b', 'c' => 'd', 'e' => 'f');
        $collection = new Collection($values);
        $iterator = $collection->getIterator();
        $this->assertInstanceOf('\Iterator', $iterator);
        $actual = array();
        foreach ($iterator as $k => $v) {
            $actual[$k] = $v;
        }
        $this->assertEquals($values, $actual);
    }

    public function testAddingElements()
    {
        $collection = new Collection();
        $this->assertEquals(array(), $collection->toArray());
        $collection->add(123);
        $this->assertEquals(array(123), $collection->toArray());
        $collection->add(345);
        $this->assertEquals(array(123, 345), $collection->toArray());
        $this->assertFalse($collection->isEmpty());
    }

    public function testGettingElements()
    {
        $values = array('a' => 'b', 'c' => 'd', 'e' => 'f');
        $collection = new Collection($values);
        $this->assertEquals('b', $collection->get('a'));
        $this->assertNull($collection->get('x'));
        $this->assertEquals(array_keys($values), $collection->getKeys());
        $this->assertEquals(array_values($values), $collection->getValues());
    }

    public function testSettingElements()
    {
        $collection = new Collection();
        $collection->set('a', 'b');
        $this->assertEquals(array('a' => 'b'), $collection->toArray());
        $collection->set('c', 'd');
        $collection->set('e', 'f');
        $expected = array('a' => 'b', 'c' => 'd', 'e' => 'f');
        $this->assertEquals($expected, $collection->toArray());
        $this->assertEquals('d', $collection->get('c'));
        $this->assertEquals('f', $collection['e']);
        $actual = array();
        foreach ($collection as $k => $v) {
            $actual[$k] = $v;
        }
        $this->assertEquals($expected, $actual);
    }

    public function testSettingCollectionValue()
    {
        $collection = new Collection(array('a', 'b', 'c'));
        $value = array('x', 'y', 'z');
        $collection->setValue($value);
        $this->assertEquals($value, $collection->getValue());
    }

    /**
     * @dataProvider dataProviderSettingInvalidCollectionValues
     */
    public function testSettingInvalidCollectionValues($value, $exception = true)
    {
        $collection = new Collection();
        if ($exception) {
            $this->setExpectedException('\InvalidArgumentException');
        }
        $collection->setValue($value);
    }

    public function dataProviderSettingInvalidCollectionValues()
    {
        return array(
            array(null),
            array(true),
            array(12345),
            array('test'),
            array(new \ArrayObject()),
            array(array(1, 2, 3), false),
            array(new ToArray(array(1, 2, 3)), false),
        );
    }

    public function testElementsExistenceChecking()
    {
        $collection = new Collection(array('a' => 'b'));
        $this->assertTrue($collection->containsKey('a'));
        $this->assertFalse($collection->contains('a'));
        $this->assertFalse($collection->indexOf('a'));
        $this->assertFalse($collection->containsKey('b'));
        $this->assertTrue($collection->contains('b'));
        $this->assertEquals('a', $collection->indexOf('b'));
    }

    public function testElementsRemoving()
    {
        $collection = new Collection(array('a' => 'b', 'c' => 'd', 'e' => 'f'));
        $this->assertEquals('d', $collection->remove('c'));
        $this->assertNull($collection->remove('x'));
        $this->assertEquals(array('a' => 'b', 'e' => 'f'), $collection->toArray());
        $this->assertFalse($collection->removeElement('a'));
        $this->assertEquals(array('a' => 'b', 'e' => 'f'), $collection->toArray());
        $this->assertTrue($collection->removeElement('b'));
        $this->assertEquals(array('e' => 'f'), $collection->toArray());
        $collection = new Collection(array('a', 'b', 'c', 'd', 'b', 'a', 'b', 'x'));
        $collection->removeElement('b');
        $this->assertEquals(array('a', 'c', 'd', 'a', 'x'), $collection->getValues());
    }

    public function testElementsToggling()
    {
        $collection = new Collection(array(1, 2, 3));
        $collection->toggle(2);
        $this->assertEquals(array(1, 3), $collection->getValues());
        $collection->toggle(2);
        $this->assertEquals(array(1, 3, 2), $collection->getValues());
        $collection->setValue(array(1, 2, 3, 2, 4, 2, 5, 2, 6));
        $collection->toggle(2);
        $this->assertEquals(array(1, 3, 4, 5, 6), $collection->getValues());
        $collection->toggle(2);
        $this->assertEquals(array(1, 3, 4, 5, 6, 2), $collection->getValues());
    }

    public function testPassingNullValuesAsElements()
    {
        $collection = new Collection(array(null), array(
                                                       'nullable' => true,
                                                  ));
        $this->assertEquals(array(null), $collection->toArray());
        $collection = new Collection(array(null), array(
                                                       'nullable' => false,
                                                  ));
        $this->assertEquals(array(), $collection->toArray());
    }

    public function testCollectionReset()
    {
        $value = array(1, 2, 3);
        $default = array('x', 'y', 'z');
        $collection = new Collection($value, array('default' => $default));
        $this->assertEquals($value, $collection->toArray());
        $collection->reset();
        $this->assertEquals($default, $collection->toArray());
        $this->assertFalse($collection->isEmpty());
        $collection->clear();
        $this->assertEquals(array(), $collection->toArray());
        $this->assertTrue($collection->isEmpty());
    }

    /**
     * @dataProvider dataProviderAllowedValuesLimitation
     */
    public function testAllowedValuesLimitation($validator)
    {
        $collection = new Collection(array(1, 2, 3, 5, 10, 20, 50, 100), array('allowed' => $validator));
        $expected = array(1, 2, 3, 5, 10);
        $this->assertEquals($expected, $collection->toArray());
        $collection->add('abc');
        $this->assertEquals($expected, $collection->toArray());
        $collection->add(8);
        $e2 = $expected;
        $e2[] = 8;
        $this->assertEquals($e2, $collection->toArray());
    }

    public function dataProviderAllowedValuesLimitation()
    {
        $range = range(0, 10);
        $cb = function ($v) {
            return ((is_int($v)) && ($v >= 0) && ($v <= 10));
        };
        return array(
            array($range),
            array($cb),
        );
    }

    /**
     * @param array $allowed
     * @param mixed $value
     * @param boolean $valid
     * @dataProvider dataProviderVariousKindsOfAllowedValuesValidator
     */
    public function testVariousKindsOfAllowedValuesValidator($allowed, $value, $valid)
    {
        $collection = new Collection(null, array('allowed' => $allowed));
        $collection->add($value);
        $this->assertEquals($collection->count(), ($valid) ? 1 : 0);
    }

    public function dataProviderVariousKindsOfAllowedValuesValidator()
    {
        return array(
            array(array(1, 2, 3), 1, true),
            array(array(1, 2, 3), 123, false),
            array(array('x', 'y', 'z'), 'x', true),
            array(array('x', 'y', 'z'), 'xyz', false),
            array(array(true, false), false, true),
            array(array(true, false), null, false),
            array(array(true, false), 1, false),
            array('\DateTime', new \DateTime(), true),
            array('\DateTime', new \ArrayObject(), false),
            array('Flying\Struct\Struct', new BasicStruct(), true),
        );
    }

    public function testCustomValidatorAsClassMethod()
    {
        $collection = new CollectionWithCustomValidator(null, array('allowed' => 'validate'));
        $collection->add(12);
        $collection->add('abc');
        $collection->add(true);
        $this->assertEquals(0, $collection->count());
        $collection->add(5);
        $collection->add(10);
        $collection->add(15);
        $this->assertEquals(3, $collection->count());
    }

    /**
     * @dataProvider dataProviderConfigDefaultValues
     */
    public function testConfigDefaultValues($default, $exception = true)
    {
        if ($exception) {
            $this->setExpectedException('\InvalidArgumentException');
        }
        new Collection(null, array('default' => $default));
    }

    public function dataProviderConfigDefaultValues()
    {
        $func = function () {
        };
        $obj = new ToArray(array(1, 2, 3));
        return array(
            array(null),
            array(true),
            array(123),
            array('test'),
            array(array(), false),
            array(array(1, 2, 3), false),
            array($func),
            array(new \ArrayObject()),
            array($obj, false),
        );
    }

    /**
     * @dataProvider dataProviderConfigAllowedValues
     */
    public function testConfigAllowedValues($allowed, $exception = true)
    {
        if ($exception) {
            $this->setExpectedException('\InvalidArgumentException');
        }
        new Collection(null, array('allowed' => $allowed));
    }

    public function dataProviderConfigAllowedValues()
    {
        $func = function () {
        };
        $obj = new ToArray(array(1, 2, 3));
        return array(
            array(null, false),
            array(true),
            array(123),
            array('test'),
            array(array(), false),
            array(array(1, 2, 3), false),
            array($func, false),
            array(new \ArrayObject()),
            array($obj),
            array(array($obj, 'toArray'), false),
        );
    }

    /**
     * @param string  $method
     * @param array   $args
     * @param array   $expected
     * @param boolean $checkKeys
     * @param array   $value
     * @param array   $config
     *
     * @dataProvider dataProviderValueNormalizationCallback
     */
    public function testValueNormalizationCallback(
        $method,
        array $args,
        array $expected = null,
        $checkKeys = false,
        $value = null,
        $config = null
    ) {
        $collection = new Collection($value, $config);
        $logger = new CallbackLog();
        $collection->setCallbackLogger('normalize', $logger);
        if (!method_exists($collection, $method)) {
            $this->fail('Method "' . $method . '" is not available in test object');
        }
        call_user_func_array(array($collection, $method), $args);
        if ($expected === null) {
            $expected = $args;
        }
        $log = $logger->get();
        foreach ($expected as $ek => $ev) {
            $item = array_shift($log);
            array_shift($item);
            $av = array_shift($item);
            $ak = array_shift($item);
            $this->assertEquals($ev, $av);
            if ($checkKeys) {
                $this->assertEquals($ek, $ak);
            }
        }
        if (sizeof($log)) {
            $this->fail(
                'Callback is expected to be called ' . sizeof($expected) . ' times, but was called ' . (sizeof(
                        $expected
                    ) + sizeof($log)) . ' times'
            );
        }
    }

    public function dataProviderValueNormalizationCallback()
    {
        return array(
            array('setValue', array(array(1, 2, 3)), array(1, 2, 3), true),
            array('set', array(0, 123), array(123)),
            array('add', array(123)),
            array('toggle', array(123)),
            array('toggle', array(2), null, false, array(1, 2, 3)),
            array('contains', array(2), null, false, array(1, 2, 3)),
            array('containsKey', array(2), array(), false, array(1, 2, 3)),
            array('indexOf', array(2), null, false, array(1, 2, 3)),
            array('remove', array(2), array(), false, array(1, 2, 3)),
            array('removeElement', array(2), null, false, array(1, 2, 3)),
            array('offsetExists', array(2), array(), false, array(1, 2, 3)),
            array('offsetSet', array(2, 123), array(123)),
            array('offsetSet', array(null, 123), array(123)),
            array('offsetUnset', array(2), array()),
            array('clear', array(), array()),
            array('reset', array(), array(1, 2, 3), true, null, array('default' => array(1, 2, 3))),
        );
    }

    /**
     * @param string $method
     * @param array  $args
     * @param int    $times
     * @param array  $value
     * @param array  $config
     *
     * @dataProvider dataProviderOnChangeCallback
     */
    public function testOnChangeCallback($method, array $args, $times = 1, $value = null, $config = null)
    {
        $collection = new Collection($value, $config);
        $logger = new CallbackLog();
        $collection->setCallbackLogger('onChange', $logger);
        if (!method_exists($collection, $method)) {
            $this->fail('Method "' . $method . '" is not available in test object');
        }
        call_user_func_array(array($collection, $method), $args);
        $log = $logger->get();
        $this->assertEquals($times, sizeof($log));
    }

    public function dataProviderOnChangeCallback()
    {
        return array(
            array('setValue', array(array(1, 2, 3)), 1),
            array('set', array(0, 123), 1),
            array('add', array(123), 1),
            array('toggle', array(123), 1),
            array('toggle', array(2), 1, array(1, 2, 3)),
            array('contains', array(2), 0),
            array('containsKey', array(2), 0),
            array('indexOf', array(2), 0),
            array('remove', array(2), 1, array(1, 2, 3)),
            array('remove', array(2), 0),
            array('removeElement', array(2), 1, array(1, 2, 3)),
            array('removeElement', array(2), 0, array(1, 3, 5)),
            array('offsetExists', array(2), 0, array(1, 2, 3)),
            array('offsetSet', array(2, 123), 1),
            array('offsetSet', array(null, 123), 1),
            array('offsetUnset', array(2), 1, array(1, 2, 3)),
            array('offsetUnset', array(2), 0, array()),
            array('clear', array(), 1),
            array('reset', array(), 0),
        );
    }

    /**
     * @param string  $method
     * @param array   $args
     * @param array   $value
     * @param boolean $calledOnInvalid
     *
     * @dataProvider dataProviderOnInvalidValueCallback
     */
    public function testOnInvalidValueCallback($method, array $args, $value = null, $calledOnInvalid = true)
    {
        // If there is no values filter - this callback should not be called
        $collection = new Collection($value);
        $logger = new CallbackLog();
        $collection->setCallbackLogger('onInvalidValue', $logger);
        if (!method_exists($collection, $method)) {
            $this->fail('Method "' . $method . '" is not available in test object');
        }
        call_user_func_array(array($collection, $method), $args);
        $log = $logger->get();
        $this->assertEquals(0, sizeof($log));

        // If values are treated as invalid - it should be called
        $collection = new Collection($value, array('allowed' => array()));
        $logger = new CallbackLog();
        $collection->setCallbackLogger('onInvalidValue', $logger);
        call_user_func_array(array($collection, $method), $args);
        $log = $logger->get();
        $this->assertEquals(($calledOnInvalid) ? 1 : 0, sizeof($log));
    }

    public function dataProviderOnInvalidValueCallback()
    {
        return array(
            array('setValue', array(array(1, 2, 3))),
            array('set', array(0, 123)),
            array('add', array(123)),
            array('toggle', array(123)),
            array('toggle', array(2), array(1, 2, 3)),
            array('contains', array(2), null, false),
            array('containsKey', array(2), null, false),
            array('indexOf', array(2), null, false),
            array('remove', array(2), array(1, 2, 3), false),
            array('remove', array(2), null, false),
            array('removeElement', array(2), array(1, 2, 3)),
            array('removeElement', array(2), array(1, 3, 5)),
            array('offsetExists', array(2), array(1, 2, 3), false),
            array('offsetSet', array(2, 123)),
            array('offsetSet', array(null, 123)),
            array('offsetUnset', array(2), array(1, 2, 3), false),
            array('offsetUnset', array(2), array(), false),
            array('clear', array(), null, false),
            array('reset', array(), null, false),
        );
    }
}
