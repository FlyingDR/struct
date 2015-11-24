<?php

namespace Flying\Tests\Property;

use Flying\Struct\Property\Property;
use Flying\Tests\Property\Stubs\ToString;
use Flying\Tests\Property\Stubs\UsToString;

class DateTimeTest extends BaseTypeTest
{
    /**
     * Class name of the property to test
     *
     * @var string
     */
    protected $propertyClass = 'Flying\Struct\Property\DateTime';

    /**
     * {@inheritdoc}
     */
    public function getValueTests()
    {
        $config = array('nullable' => false, 'default' => '2014-01-01');
        $dt = new \DateTime('2014-01-01');
        return array(
            array(null, null, array('nullable' => true, 'default' => null)),
            array(null, $dt, array('nullable' => true, 'default' => '2014-01-01')),
            array(true, $dt, $config),
            array(false, $dt, $config),
            array(123456789, $dt, $config),
            array('2013-10-20', new \DateTime('2013-10-20'), $config),
            array('2013-10-20', new \DateTime('2013-10-20'), $config),
            array(array(), $dt, $config),
            array(new \ArrayObject(), $dt, $config),
            array(new Property('2013-10-20'), new \DateTime('2013-10-20'), $config),
            array(new ToString('2013-10-20'), new \DateTime('2013-10-20'), $config),
            array(new UsToString('2013-10-20'), new \DateTime('2013-10-20'), $config),
            array('2013-10-20 05:25:50', new \DateTime('2013-10-20 05:25:50'), array('format' => 'Y-m-d H:i:s')),
        );
    }

    /**
     * @expectedException \Exception
     */
    public function testPassingInvalidFormattedString()
    {
        $this->getTestProperty('abc');
    }

    public function testNullDefaultForNotNullablePropertyResultsInDateTimeDefault()
    {
        $property = $this->getTestProperty(null, array('nullable' => false, 'default' => null));
        static::assertInstanceOf('\DateTime', $property->getConfig('default'));
    }

    /**
     * @return \DateTime
     */
    protected function getDefaultValue()
    {
        return new \DateTime();
    }

    public function serializationDataProvider()
    {
        $config = array('nullable' => false, 'default' => '2014-01-01');
        $dt = new \DateTime('2014-01-01');
        $tests = array(
            array(
                null,
                $dt,
                $config,
            ),
            array(
                '2013-10-20',
                new \DateTime('2013-10-20'),
                $config,
            ),
            array(
                '2013-10-20 05:25:50',
                new \DateTime('2013-10-20 05:25:50'),
                $config,
            ),
            array(
                new \DateTime('2013-10-20 05:25:50', new \DateTimeZone('Europe/London')),
                new \DateTime('2013-10-20 05:25:50', new \DateTimeZone('Europe/London')),
                $config,
            ),
        );
        return $tests;
    }
}
