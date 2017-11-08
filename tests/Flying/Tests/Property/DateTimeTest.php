<?php

namespace Flying\Tests\Property;

use Flying\Struct\Property\DateTime;
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
    protected $propertyClass = DateTime::class;

    /**
     * {@inheritdoc}
     */
    public function getValueTests()
    {
        $config = ['nullable' => false, 'default' => '2014-01-01'];
        $dt = new \DateTime('2014-01-01');
        return [
            [null, null, ['nullable' => true, 'default' => null]],
            [null, $dt, ['nullable' => true, 'default' => '2014-01-01']],
            [true, $dt, $config],
            [false, $dt, $config],
            [123456789, $dt, $config],
            ['2013-10-20', new \DateTime('2013-10-20'), $config],
            ['2013-10-20', new \DateTime('2013-10-20'), $config],
            [[], $dt, $config],
            [new \ArrayObject(), $dt, $config],
            [new Property('2013-10-20'), new \DateTime('2013-10-20'), $config],
            [new ToString('2013-10-20'), new \DateTime('2013-10-20'), $config],
            [new UsToString('2013-10-20'), new \DateTime('2013-10-20'), $config],
            ['2013-10-20 05:25:50', new \DateTime('2013-10-20 05:25:50'), ['format' => 'Y-m-d H:i:s']],
        ];
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
        $property = $this->getTestProperty(null, ['nullable' => false, 'default' => null]);
        static::assertInstanceOf(\DateTime::class, $property->getConfig('default'));
    }

    public function serializationDataProvider()
    {
        $config = ['nullable' => false, 'default' => '2014-01-01'];
        $dt = new \DateTime('2014-01-01');
        $tests = [
            [
                null,
                $dt,
                $config,
            ],
            [
                '2013-10-20',
                new \DateTime('2013-10-20'),
                $config,
            ],
            [
                '2013-10-20 05:25:50',
                new \DateTime('2013-10-20 05:25:50'),
                $config,
            ],
            [
                new \DateTime('2013-10-20 05:25:50', new \DateTimeZone('Europe/London')),
                new \DateTime('2013-10-20 05:25:50', new \DateTimeZone('Europe/London')),
                $config,
            ],
        ];
        return $tests;
    }

    /**
     * @return \DateTime
     */
    protected function getDefaultValue()
    {
        return new \DateTime();
    }
}
