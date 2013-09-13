<?php

namespace Flying\Tests\Property;


use Flying\Struct\Property\Property;

class BooleanTest extends BaseTypeTest
{
    /**
     * Class name of the property to test
     * @var string
     */
    protected $propertyClass = 'Boolean';
    /**
     * Default property value
     * @var string
     */
    protected $defaultValue = true;
    /**
     * Value validation tests
     * @var array
     */
    protected $valueTests = array(
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
    protected $serializationTests = array(
        true,
        false,
    );

    public function getValueTests()
    {
        $tests = $this->valueTests;
        $tests[] = array(new \ArrayObject(), true);
        $tests[] = array(new Property(false), false);
        return $tests;
    }
}
