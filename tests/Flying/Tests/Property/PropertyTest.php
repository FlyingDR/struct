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
    protected $propertyClass = 'Flying\Tests\Property\Fixtures\Property';

    public function testBasicOperations()
    {
        $property = $this->getTestProperty();
        $this->assertEquals($this->defaultValue, $property->getValue());
        $property->setValue(12345);
        $this->assertEquals(12345, $property->getValue());
        $property->setValue(true);
        $this->assertTrue($property->getValue());
        $property->reset();
        $this->assertEquals($this->defaultValue, $property->getValue());
    }

    public function testConfigurationOptionsAccess()
    {
        $property = $this->getTestProperty();
        $this->assertFalse($property->getConfig('nullable'));
        $this->assertEquals($this->defaultValue, $property->getConfig('default'));
    }

    public function testPropertyRemainsTheSameOnSettingInvalidValue()
    {
        $property = $this->getTestProperty();
        $this->assertEquals($this->defaultValue, $property->getValue());
        $property->setValue(12345);
        $this->assertEquals(12345, $property->getValue());
        $property->setValue(null); // NULL is not allowed so property should not change its value
        $this->assertEquals(12345, $property->getValue());
    }

    public function testExceptionOnInvalidDefaultValue()
    {
        $this->setExpectedException('Flying\Struct\Exception');
        new Property(null, array(
            'nullable' => false,
            'default'  => null,
        ));
    }

    public function testValueNormalizationCallback()
    {
        $this->runCallbackTest('normalize', array(
            12345,
            null,
            $this->defaultValue,
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
        $property->setValue($value);
        $property->getValue();
        $property->setValue(null);
        $property->getValue();
        $property->reset();
        $property->getValue();

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
            'default'                => $this->defaultValue,
            'update_notify_listener' => $mock,
        ));
        $property->setValue(12345);
        // Attempt to set invalid property value should not trigger update notification
        $property->setValue(null);
        // Reset should not trigger update notification
        $property->reset();
    }

    public function serializationDataProvider()
    {
        $testValues = array(
            true,
            false,
            12345,
            -123.45,
            'some string',
            new \ArrayObject(),
        );
        $result = array();
        foreach ($testValues as $value) {
            $result[] = array($value, $value);
        }
        return $result;
    }
}
