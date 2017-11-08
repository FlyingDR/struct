<?php

namespace Flying\Tests\Property;


use Flying\Struct\Property\Property;

class IntTest extends BaseTypeTest
{
    /**
     * Class name of the property to test
     *
     * @var string
     */
    protected $propertyClass = 'Integer';
    /**
     * Default property value
     *
     * @var string
     */
    protected $defaultValue = 12345;
    /**
     * Serialization tests
     *
     * @var array
     */
    protected $serializationTests = array(
        0,
        1,
        -100,
        100,
    );

    /**
     * {@inheritdoc}
     */
    public function getValueTests()
    {
        $tests = array(
            array(true, 1),
            array(false, 0),

            array(1, 1),
            array(0, 0),
            array(12345, 12345),
            array(-12345, -12345),
            array(123.45, 123),
            array(135.79, 135),
            array(-135.79, -135),

            array(0, 0, array('min' => 0, 'max' => 10)),
            array(1, 1, array('min' => 0, 'max' => 10)),
            array(10, 10, array('min' => 0, 'max' => 10)),
            array(12345, 10, array('min' => 0, 'max' => 10)),
            array(-12345, 0, array('min' => 0, 'max' => 10)),

            array(12345, 10, array('min' => -10, 'max' => 10)),
            array(-12345, -10, array('min' => -10, 'max' => 10)),

            array('', 0),
            array('1', 1),
            array('0', 0),
            array('12345', 12345),
            array('-12345', -12345),
            array('123.45', 123),
            array('135.79', 135),
            array('-135.79', -135),
            array('test', 0),

            // Non-scalar values are not acceptable for this property
            // so default value should be set instead
            array(array(), 123, array('default' => 123)),
            array(new \ArrayObject(), 123, array('default' => 123)),
            array(new Property('abc'), 0),
            array(new Property('12345'), 12345),
        );
        return $tests;
    }
}
