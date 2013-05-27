<?php

namespace Flying\Tests\Property;

use Flying\Struct\Property\Property;
use Flying\Tests\Property\Stubs\ToString;
use Flying\Tests\Property\Stubs\UsToString;

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
    /**
     * Serialization tests
     * @var array
     */
    protected $_serializationTests = array(
        '',
        'some text',
        'some long text for property value',
    );

    public function getValueTests()
    {
        $tests = $this->_valueTests;
        $tests[] = array(array(), 'test', array('default' => 'test'));
        $tests[] = array(new \ArrayObject(), 'test', array('default' => 'test'));
        $tests[] = array(new Property('abc'), 'abc');
        $tests[] = array(new Property('12345'), '12345');
        $tests[] = array(new ToString('test string'), 'test string');
        $tests[] = array(new UsToString('another test string'), 'another test string');
        return $tests;
    }

}
