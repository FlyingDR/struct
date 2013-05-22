<?php

namespace Flying\Tests\Property;


class StringTest extends BaseTypeTest
{
    /**
     * Class name of the property to test
     * @var string
     */
    protected $_propertyClass = 'String';
    /**
     * Default property value
     * @var string
     */
    protected $_defaultValue = 'default value';
    /**
     * Value validation tests
     * @var array
     */
    protected $_valueTests = array(
        array(true, '1'),
        array(false, ''),

        array(1, '1'),
        array(0, '0'),
        array(12345, '12345'),
        array(-12345, '-12345'),
        array(123.45, '123.45'),
        array(-123.45, '-123.45'),

        array('test', 'test'),
        array('some long text for property value', 'some long text for property value'),
        array('some long text for property value', 'some long ', array('maxlength' => 10)),
        array('some long text for property value', 'some long text for p', array('maxlength' => 20)),
    );

}
