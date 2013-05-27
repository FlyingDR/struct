<?php

namespace Flying\Tests\Property;


class IntTest extends BaseTypeTest
{
    /**
     * Class name of the property to test
     * @var string
     */
    protected $_propertyClass = 'Int';
    /**
     * Default property value
     * @var string
     */
    protected $_defaultValue = 12345;
    /**
     * Value validation tests
     * @var array
     */
    protected $_valueTests = array(
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
    );
    /**
     * Serialization tests
     * @var array
     */
    protected $_serializationTests = array(
        0,
        1,
        -100,
        100,
    );

}
