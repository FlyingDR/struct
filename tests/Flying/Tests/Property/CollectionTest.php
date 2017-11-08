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
     * @param int $expected
     * @param array $config
     *
     * @dataProvider dataProviderCountableInterface
     */
    public function testCountableInterface($value, $expected, $config = null)
    {
        $collection = new Collection($value, $config);
        static::assertEquals($expected, $collection->count());
        static::assertCount($expected, $collection);
    }

    public function dataProviderCountableInterface()
    {
        return [
            [null, 0],
            [[], 0],
            [[123], 1],
            [[1, 2, 3], 3],
            [null, 3, ['default' => [1, 2, 3]]]
        ];
    }

    public function testArrayAccess()
    {
        $collection = new Collection();
        $collection['a'] = 'b';
        static::assertCount(1, $collection);
        static::assertTrue(isset($collection['a']));
        static::assertEquals('b', $collection['a']);
        unset($collection['a']);
        static::assertCount(0, $collection);
        static::assertFalse(isset($collection['a']));
        $collection[] = 'abc';
        static::assertCount(1, $collection);
        /** @var $collection Collection */
        static::assertEquals(['abc'], $collection->toArray());
    }

    public function testCollectionIterator()
    {
        $values = ['a' => 'b', 'c' => 'd', 'e' => 'f'];
        $collection = new Collection($values);
        $iterator = $collection->getIterator();
        static::assertInstanceOf('\Iterator', $iterator);
        $actual = [];
        foreach ($iterator as $k => $v) {
            $actual[$k] = $v;
        }
        static::assertEquals($values, $actual);
    }

    public function testAddingElements()
    {
        $collection = new Collection();
        static::assertEquals([], $collection->toArray());
        $collection->add(123);
        static::assertEquals([123], $collection->toArray());
        $collection->add(345);
        static::assertEquals([123, 345], $collection->toArray());
        static::assertFalse($collection->isEmpty());
    }

    public function testGettingElements()
    {
        $values = ['a' => 'b', 'c' => 'd', 'e' => 'f'];
        $collection = new Collection($values);
        static::assertEquals('b', $collection->get('a'));
        static::assertNull($collection->get('x'));
        static::assertEquals(array_keys($values), $collection->getKeys());
        static::assertEquals(array_values($values), $collection->getValues());
    }

    public function testSettingElements()
    {
        $collection = new Collection();
        $collection->set('a', 'b');
        static::assertEquals(['a' => 'b'], $collection->toArray());
        $collection->set('c', 'd');
        $collection->set('e', 'f');
        $expected = ['a' => 'b', 'c' => 'd', 'e' => 'f'];
        static::assertEquals($expected, $collection->toArray());
        static::assertEquals('d', $collection->get('c'));
        static::assertEquals('f', $collection['e']);
        $actual = [];
        foreach ($collection as $k => $v) {
            $actual[$k] = $v;
        }
        static::assertEquals($expected, $actual);
    }

    public function testSettingCollectionValue()
    {
        $collection = new Collection(['a', 'b', 'c']);
        $value = ['x', 'y', 'z'];
        $collection->setValue($value);
        static::assertEquals($value, $collection->getValue());
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
        return [
            [null],
            [true],
            [12345],
            ['test'],
            [new \ArrayObject()],
            [[1, 2, 3], false],
            [new ToArray([1, 2, 3]), false],
        ];
    }

    public function testElementsExistenceChecking()
    {
        $collection = new Collection(['a' => 'b']);
        static::assertTrue($collection->containsKey('a'));
        static::assertFalse($collection->contains('a'));
        static::assertFalse($collection->indexOf('a'));
        static::assertFalse($collection->containsKey('b'));
        static::assertTrue($collection->contains('b'));
        static::assertEquals('a', $collection->indexOf('b'));
    }

    public function testElementsRemoving()
    {
        $collection = new Collection(['a' => 'b', 'c' => 'd', 'e' => 'f']);
        static::assertEquals('d', $collection->remove('c'));
        static::assertNull($collection->remove('x'));
        static::assertEquals(['a' => 'b', 'e' => 'f'], $collection->toArray());
        static::assertFalse($collection->removeElement('a'));
        static::assertEquals(['a' => 'b', 'e' => 'f'], $collection->toArray());
        static::assertTrue($collection->removeElement('b'));
        static::assertEquals(['e' => 'f'], $collection->toArray());
        $collection = new Collection(['a', 'b', 'c', 'd', 'b', 'a', 'b', 'x']);
        $collection->removeElement('b');
        static::assertEquals(['a', 'c', 'd', 'a', 'x'], $collection->getValues());
    }

    public function testElementsToggling()
    {
        $collection = new Collection([1, 2, 3]);
        $collection->toggle(2);
        static::assertEquals([1, 3], $collection->getValues());
        $collection->toggle(2);
        static::assertEquals([1, 3, 2], $collection->getValues());
        $collection->setValue([1, 2, 3, 2, 4, 2, 5, 2, 6]);
        $collection->toggle(2);
        static::assertEquals([1, 3, 4, 5, 6], $collection->getValues());
        $collection->toggle(2);
        static::assertEquals([1, 3, 4, 5, 6, 2], $collection->getValues());
    }

    public function testPassingNullValuesAsElements()
    {
        $collection = new Collection([null], [
            'nullable' => true,
        ]);
        static::assertEquals([null], $collection->toArray());
        $collection = new Collection([null], [
            'nullable' => false,
        ]);
        static::assertEquals([], $collection->toArray());
    }

    public function testCollectionReset()
    {
        $value = [1, 2, 3];
        $default = ['x', 'y', 'z'];
        $collection = new Collection($value, ['default' => $default]);
        static::assertEquals($value, $collection->toArray());
        $collection->reset();
        static::assertEquals($default, $collection->toArray());
        static::assertFalse($collection->isEmpty());
        $collection->clear();
        static::assertEquals([], $collection->toArray());
        static::assertTrue($collection->isEmpty());
    }

    /**
     * @dataProvider dataProviderAllowedValuesLimitation
     */
    public function testAllowedValuesLimitation($validator)
    {
        $collection = new Collection([1, 2, 3, 5, 10, 20, 50, 100], ['allowed' => $validator]);
        $expected = [1, 2, 3, 5, 10];
        static::assertEquals($expected, $collection->toArray());
        $collection->add('abc');
        static::assertEquals($expected, $collection->toArray());
        $collection->add(8);
        $e2 = $expected;
        $e2[] = 8;
        static::assertEquals($e2, $collection->toArray());
    }

    public function dataProviderAllowedValuesLimitation()
    {
        $range = range(0, 10);
        $cb = function ($v) {
            return ((is_int($v)) && ($v >= 0) && ($v <= 10));
        };
        return [
            [$range],
            [$cb],
        ];
    }

    /**
     * @param array $allowed
     * @param mixed $value
     * @param boolean $valid
     * @dataProvider dataProviderVariousKindsOfAllowedValuesValidator
     */
    public function testVariousKindsOfAllowedValuesValidator($allowed, $value, $valid)
    {
        $collection = new Collection(null, ['allowed' => $allowed]);
        $collection->add($value);
        static::assertEquals($collection->count(), ($valid) ? 1 : 0);
    }

    public function dataProviderVariousKindsOfAllowedValuesValidator()
    {
        return [
            [[1, 2, 3], 1, true],
            [[1, 2, 3], 123, false],
            [['x'], 'x', true],
            [['x'], 'y', false],
            [['x', 'y', 'z'], 'x', true],
            [['x', 'y', 'z'], 'xyz', false],
            [[true, false], false, true],
            [[true, false], null, false],
            [[true, false], 1, false],
            ['\DateTime', new \DateTime(), true],
            ['\DateTime', new \ArrayObject(), false],
            ['Flying\Struct\Struct', new BasicStruct(), true],
        ];
    }

    public function testCustomValidatorAsClassMethod()
    {
        $collection = new CollectionWithCustomValidator(null, ['allowed' => 'validate']);
        $collection->add(12);
        $collection->add('abc');
        $collection->add(true);
        static::assertEquals(0, $collection->count());
        $collection->add(5);
        $collection->add(10);
        $collection->add(15);
        static::assertEquals(3, $collection->count());
    }

    /**
     * @dataProvider dataProviderConfigDefaultValues
     */
    public function testConfigDefaultValues($default, $exception = true)
    {
        if ($exception) {
            $this->setExpectedException('\InvalidArgumentException');
        }
        new Collection(null, ['default' => $default]);
    }

    public function dataProviderConfigDefaultValues()
    {
        $func = function () {
        };
        $obj = new ToArray([1, 2, 3]);
        return [
            [null],
            [true],
            [123],
            ['test'],
            [[], false],
            [[1, 2, 3], false],
            [$func],
            [new \ArrayObject()],
            [$obj, false],
        ];
    }

    /**
     * @dataProvider dataProviderConfigAllowedValues
     */
    public function testConfigAllowedValues($allowed, $exception = true)
    {
        if ($exception) {
            $this->setExpectedException('\InvalidArgumentException');
        }
        new Collection(null, ['allowed' => $allowed]);
    }

    public function dataProviderConfigAllowedValues()
    {
        $func = function () {
        };
        $obj = new ToArray([1, 2, 3]);
        return [
            [null, false],
            [true],
            [123],
            ['test'],
            [[], false],
            [[1, 2, 3], false],
            [$func, false],
            [new \ArrayObject()],
            [$obj],
            [[$obj, 'toArray'], false],
        ];
    }

    /**
     * @param string $method
     * @param array $args
     * @param array $expected
     * @param boolean $checkKeys
     * @param array $value
     * @param array $config
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
            static::fail('Method "' . $method . '" is not available in test object');
        }
        call_user_func_array([$collection, $method], $args);
        if ($expected === null) {
            $expected = $args;
        }
        $log = $logger->get();
        foreach ($expected as $ek => $ev) {
            /** @noinspection DisconnectedForeachInstructionInspection */
            $item = array_shift($log);
            array_shift($item);
            $av = array_shift($item);
            $ak = array_shift($item);
            static::assertEquals($ev, $av);
            if ($checkKeys) {
                static::assertEquals($ek, $ak);
            }
        }
        if (count($log)) {
            static::fail(
                'Callback is expected to be called ' . count($expected) . ' times, but was called ' . (count($expected) + count($log)) . ' times'
            );
        }
    }

    public function dataProviderValueNormalizationCallback()
    {
        return [
            ['setValue', [[1, 2, 3]], [1, 2, 3], true],
            ['set', [0, 123], [123]],
            ['add', [123]],
            ['toggle', [123]],
            ['toggle', [2], null, false, [1, 2, 3]],
            ['contains', [2], null, false, [1, 2, 3]],
            ['containsKey', [2], [], false, [1, 2, 3]],
            ['indexOf', [2], null, false, [1, 2, 3]],
            ['remove', [2], [], false, [1, 2, 3]],
            ['removeElement', [2], null, false, [1, 2, 3]],
            ['offsetExists', [2], [], false, [1, 2, 3]],
            ['offsetSet', [2, 123], [123]],
            ['offsetSet', [null, 123], [123]],
            ['offsetUnset', [2], []],
            ['clear', [], []],
            ['reset', [], [1, 2, 3], true, null, ['default' => [1, 2, 3]]],
        ];
    }

    /**
     * @param string $method
     * @param array $args
     * @param int $times
     * @param array $value
     * @param array $config
     *
     * @dataProvider dataProviderOnChangeCallback
     */
    public function testOnChangeCallback($method, array $args, $times = 1, $value = null, $config = null)
    {
        $collection = new Collection($value, $config);
        $logger = new CallbackLog();
        $collection->setCallbackLogger('onChange', $logger);
        if (!method_exists($collection, $method)) {
            static::fail('Method "' . $method . '" is not available in test object');
        }
        call_user_func_array([$collection, $method], $args);
        $log = $logger->get();
        static::assertCount($times, $log);
    }

    public function dataProviderOnChangeCallback()
    {
        return [
            ['setValue', [[1, 2, 3]], 1],
            ['set', [0, 123], 1],
            ['add', [123], 1],
            ['toggle', [123], 1],
            ['toggle', [2], 1, [1, 2, 3]],
            ['contains', [2], 0],
            ['containsKey', [2], 0],
            ['indexOf', [2], 0],
            ['remove', [2], 1, [1, 2, 3]],
            ['remove', [2], 0],
            ['removeElement', [2], 1, [1, 2, 3]],
            ['removeElement', [2], 0, [1, 3, 5]],
            ['offsetExists', [2], 0, [1, 2, 3]],
            ['offsetSet', [2, 123], 1],
            ['offsetSet', [null, 123], 1],
            ['offsetUnset', [2], 1, [1, 2, 3]],
            ['offsetUnset', [2], 0, []],
            ['clear', [], 1],
            ['reset', [], 0],
        ];
    }

    /**
     * @param string $method
     * @param array $args
     * @param array $value
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
            static::fail('Method "' . $method . '" is not available in test object');
        }
        call_user_func_array([$collection, $method], $args);
        $log = $logger->get();
        static::assertCount(0, $log);

        // If values are treated as invalid - it should be called
        $collection = new Collection($value, ['allowed' => []]);
        $logger = new CallbackLog();
        $collection->setCallbackLogger('onInvalidValue', $logger);
        call_user_func_array([$collection, $method], $args);
        $log = $logger->get();
        static::assertCount(($calledOnInvalid) ? 1 : 0, $log);
    }

    public function dataProviderOnInvalidValueCallback()
    {
        return [
            ['setValue', [[1, 2, 3]]],
            ['set', [0, 123]],
            ['add', [123]],
            ['toggle', [123]],
            ['toggle', [2], [1, 2, 3]],
            ['contains', [2], null, false],
            ['containsKey', [2], null, false],
            ['indexOf', [2], null, false],
            ['remove', [2], [1, 2, 3], false],
            ['remove', [2], null, false],
            ['removeElement', [2], [1, 2, 3]],
            ['removeElement', [2], [1, 3, 5]],
            ['offsetExists', [2], [1, 2, 3], false],
            ['offsetSet', [2, 123]],
            ['offsetSet', [null, 123]],
            ['offsetUnset', [2], [1, 2, 3], false],
            ['offsetUnset', [2], [], false],
            ['clear', [], null, false],
            ['reset', [], null, false],
        ];
    }
}
