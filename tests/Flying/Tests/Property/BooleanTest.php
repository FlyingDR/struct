<?php

namespace Flying\Tests\Property;


class BooleanTest extends BaseTypeTest
{
    /**
     * Class name of the property to test
     * @var string
     */
    protected $_propertyClass = 'Boolean';
    /**
     * Default property value
     * @var string
     */
    protected $_defaultValue = true;
    /**
     * Value validation tests
     * @var array
     */
    protected $_valueTests = array(
        array(true, true),
        array(false, false),
        array(1, true),
        array(0, false),
        array(-1, true),
        array('', false),
        array('0', false),
        array('test', true),
        array(array(), false),
        array(array(1, 2, 3), true),
    );
    /**
     * Serialization tests
     * @var array
     */
    protected $_serializationTests = array(
        true,
        false,
    );

}
