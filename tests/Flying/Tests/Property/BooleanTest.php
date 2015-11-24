<?php

namespace Flying\Tests\Property;


use Flying\Struct\Property\Property;

class BooleanTest extends BaseTypeTest
{
    /**
     * Class name of the property to test
     *
     * @var string
     */
    protected $propertyClass = 'Boolean';
    /**
     * Default property value
     *
     * @var string
     */
    protected $defaultValue = true;
    /**
     * Serialization tests
     *
     * @var array
     */
    protected $serializationTests = array(
        true,
        false,
    );

    /**
     * {@inheritdoc}
     */
    public function getValueTests()
    {
        return array(
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
            array(new \ArrayObject(), true),
            array(new Property(false), false),
        );
    }
}
