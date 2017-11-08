<?php

namespace Flying\Tests\Property;

use Flying\Struct\Property\Property;
use Flying\Tests\Property\Stubs\ToString;
use Flying\Tests\Property\Stubs\UsToString;

class StringTest extends BaseTypeTest
{
    /**
     * Class name of the property to test
     *
     * @var string
     */
    protected $propertyClass = 'Str';
    /**
     * Default property value
     *
     * @var string
     */
    protected $defaultValue = 'default value';
    /**
     * Serialization tests
     *
     * @var array
     */
    protected $serializationTests = array(
        '',
        'some text',
        'some long text for property value',
    );

    /**
     * {@inheritdoc}
     */
    public function getValueTests()
    {
        return array(
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

            array(array(), 'test', array('default' => 'test')),
            array(new \ArrayObject(), 'test', array('default' => 'test')),
            array(new Property('abc'), 'abc'),
            array(new Property('12345'), '12345'),
            array(new ToString('test string'), 'test string'),
            array(new UsToString('another test string'), 'another test string'),
        );
    }
}
