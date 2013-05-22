<?php

namespace Flying\Tests\Property;

use Flying\Tests\Property\Fixtures\Property;
use Flying\Tests\Tools\CallbackLog;
use Mockery;

class PropertyTest extends BasePropertyTest
{
    /**
     * Class name of the property to test
     * @var string
     */
    protected $_propertyClass = 'Flying\Tests\Property\Fixtures\Property';

    public function testBasicOperations()
    {
        $property = $this->getTestProperty();
        $this->assertEquals($this->_defaultValue, $property->get());
        $property->set(12345);
        $this->assertEquals(12345, $property->get());
        $property->set(true);
        $this->assertTrue($property->get());
        $property->reset();
        $this->assertEquals($this->_defaultValue, $property->get());
    }

    public function testConfigurationOptionsAccess()
    {
        $property = $this->getTestProperty();
        $this->assertFalse($property->getConfig('nullable'));
        $this->assertEquals($this->_defaultValue, $property->getConfig('default'));
    }

    public function testPropertyRemainsTheSameOnSettingInvalidValue()
    {
        $property = $this->getTestProperty();
        $this->assertEquals($this->_defaultValue, $property->get());
        $property->set(12345);
        $this->assertEquals(12345, $property->get());
        $property->set(null); // NULL is not allowed so property should not change its value
        $this->assertEquals(12345, $property->get());
    }

    public function testExceptionOnInvalidDefaultValue()
    {
        $this->setExpectedException('Flying\Struct\Exception');
        new Property(null, array(
            'nullable' => false,
            'default'  => null,
        ));
    }

    public function testSerialization()
    {
        $property = $this->getTestProperty();
        $testSerialize = function ($value) use ($property) {
            $value = serialize($value);
            $class = get_class($property);
            $result = sprintf('C:%d:"%s":%d:{%s}', strlen($class), $class, strlen($value), $value);
            return $result;
        };
        $this->assertEquals($testSerialize($this->_defaultValue), serialize($property));
        $testValues = array(
            true,
            false,
            12345,
            -123.45,
            'some string',
            new \ArrayObject(),
        );
        foreach ($testValues as $value) {
            $property->set($value);
            $serialized = serialize($property);
            $this->assertEquals($testSerialize($value), $serialized);
            $p = unserialize($serialized);
            $this->assertEquals($value, $p->get());
        }
    }

    public function testValueNormalizationCallback()
    {
        $this->runCallbackTest('normalize', array(
            12345,
            null,
            $this->_defaultValue,
        ));
    }

    public function testOnChangeCallback()
    {
        $this->runCallbackTest('onChange', array(
            12345,
        ), false);
    }

    public function testOnInvalidValueCallback()
    {
        $this->runCallbackTest('onInvalidValue', array(
            null,
        ));
    }

    protected function runCallbackTest($method, $expected, $useValue = true)
    {
        /** @var $property Property */
        $property = $this->getTestProperty();
        $logger = new CallbackLog();
        $property->setCallbackLogger($method, $logger);

        $value = 12345;
        $property->set($value);
        $property->get();
        $property->set(null);
        $property->get();
        $property->reset();
        $property->get();

        $log = $logger->get();
        $this->assertEquals(sizeof($expected), sizeof($log));
        foreach ($expected as $v) {
            $temp = array_shift($log);
            $exp = array($method);
            if ($useValue) {
                $exp[] = $v;
            }
            $this->assertEquals($exp, $temp);
        }
    }

    public function testUpdateNotificationListener()
    {
        $mock = Mockery::mock('Flying\Struct\Common\UpdateNotifyListenerInterface');
        $mock->shouldReceive('updateNotify')->once()
            ->with(Mockery::type('Flying\Tests\Property\Fixtures\Property'));
        $property = new Property(null, array(
            'nullable'               => false,
            'default'                => $this->_defaultValue,
            'update_notify_listener' => $mock,
        ));
        $property->set(12345);
        // Attempt to set invalid property value should not trigger update notification
        $property->set(null);
        // Reset should not trigger update notification
        $property->reset();
    }

}
