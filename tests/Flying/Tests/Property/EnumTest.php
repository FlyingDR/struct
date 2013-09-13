<?php

namespace Flying\Tests\Property;

use Flying\Struct\Property\Property;
use Flying\Tests\Property\Stubs\ToArray;

class EnumTest extends BaseTypeTest
{
    /**
     * Class name of the property to test
     * @var string
     */
    protected $propertyClass = 'Enum';
    /**
     * Default configuration options for test
     * @var array
     */
    protected $defaultConfig = array(
        'nullable' => false,
        'default'  => 'a',
        'values'   => array('a', 'b', 'c'),
    );
    /**
     * Default property value
     * @var string
     */
    protected $defaultValue = 'a';
    /**
     * Value validation tests
     * @var array
     */
    protected $valueTests = array(
        array('a', 'a', array('default' => 'b', 'values' => array('a', 'b', 'c'))),
        array(null, 'b', array('default' => 'b', 'values' => array('a', 'b', 'c'))),
        array('x', 'b', array('default' => 'b', 'values' => array('a', 'b', 'c'))),
        array(2, 2, array('default' => 3, 'values' => array(1, 2, 3))),
        array('2', 3, array('default' => 3, 'values' => array(1, 2, 3))),
        array(array(), 3, array('default' => 3, 'values' => array(1, 2, 3))),
    );
    /**
     * Serialization tests
     * @var array
     */
    protected $serializationTests = array(
        'a',
    );

    public function getValueTests()
    {
        $tests = $this->valueTests;
        $tests[] = array(new \ArrayObject(), 3, array('default' => 3, 'values' => array(1, 2, 3)));
        $tests[] = array(new Property(1), 1, array('default' => 3, 'values' => array(1, 2, 3)));
        $tests[] = array(1, 1, array('default' => 3, 'values' => new ToArray(array(1, 2, 3))));
        return $tests;
    }

    public function testAcceptableNullValue()
    {
        // This test is not needed for enum property
    }
}
