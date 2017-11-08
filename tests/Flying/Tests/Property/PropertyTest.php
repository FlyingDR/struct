<?php

namespace Flying\Tests\Property;

use Flying\Struct\Common\UpdateNotifyListenerInterface;
use Flying\Tests\Property\Fixtures\Property;
use Flying\Tests\Tools\CallbackLog;
use Mockery;

class PropertyTest extends BasePropertyTest
{
    /**
     * Class name of the property to test
     *
     * @var string
     */
    protected $propertyClass = Property::class;

    public function testBasicOperations()
    {
        $property = $this->getTestProperty();
        static::assertEquals($this->getDefaultValue(), $property->getValue());
        $property->setValue(12345);
        static::assertEquals(12345, $property->getValue());
        $property->setValue(true);
        static::assertTrue($property->getValue());
        $property->reset();
        static::assertEquals($this->getDefaultValue(), $property->getValue());
    }

    public function testConfigurationOptionsAccess()
    {
        $property = $this->getTestProperty();
        static::assertFalse($property->getConfig('nullable'));
        static::assertEquals($this->getDefaultValue(), $property->getConfig('default'));
    }

    public function testPropertyRemainsTheSameOnSettingInvalidValue()
    {
        $property = $this->getTestProperty();
        static::assertEquals($this->getDefaultValue(), $property->getValue());
        $property->setValue(12345);
        static::assertEquals(12345, $property->getValue());
        $property->setValue(null); // NULL is not allowed so property should not change its value
        static::assertEquals(12345, $property->getValue());
    }

    /**
     * @expectedException \Flying\Struct\Exception
     */
    public function testExceptionOnInvalidDefaultValue()
    {
        new Property(null, [
            'nullable' => false,
            'default'  => null,
        ]);
    }

    public function testValueNormalizationCallback()
    {
        $this->runCallbackTest('normalize', [
            12345,
            null,
            $this->getDefaultValue(),
        ]);
    }

    /**
     * @param string $method
     * @param array $expected
     * @param bool $useValue
     */
    private function runCallbackTest($method, array $expected, $useValue = true)
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
        static::assertEquals(count($expected), count($log));
        foreach ($expected as $v) {
            $temp = array_shift($log);
            $exp = [$method];
            if ($useValue) {
                $exp[] = $v;
            }
            static::assertEquals($exp, $temp);
        }
    }

    public function testOnChangeCallback()
    {
        $this->runCallbackTest('onChange', [
            12345,
        ], false);
    }

    public function testOnInvalidValueCallback()
    {
        $this->runCallbackTest('onInvalidValue', [
            null,
        ]);
    }

    public function testUpdateNotificationListener()
    {
        $mock = Mockery::mock(UpdateNotifyListenerInterface::class);
        $mock->shouldReceive('updateNotify')->once()
            ->with(Mockery::type(Property::class));
        $property = new Property(null, [
            'nullable'               => false,
            'default'                => $this->getDefaultValue(),
            'update_notify_listener' => $mock,
        ]);
        $property->setValue(12345);
        // Attempt to set invalid property value should not trigger update notification
        $property->setValue(null);
        // Reset should not trigger update notification
        $property->reset();
    }

    public function serializationDataProvider()
    {
        $testValues = [
            true,
            false,
            12345,
            -123.45,
            'some string',
            new \ArrayObject(),
        ];
        $result = [];
        foreach ($testValues as $value) {
            $result[] = [$value, $value];
        }
        return $result;
    }
}
