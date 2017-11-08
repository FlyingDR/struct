<?php

namespace Flying\Tests\Property;


use Flying\Struct\Property\Property;

class IntegerTest extends BaseTypeTest
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
    protected $serializationTests = [
        0,
        1,
        -100,
        100,
    ];

    /**
     * {@inheritdoc}
     */
    public function getValueTests()
    {
        $tests = [
            [true, 1],
            [false, 0],

            [1, 1],
            [0, 0],
            [12345, 12345],
            [-12345, -12345],
            [123.45, 123],
            [135.79, 135],
            [-135.79, -135],

            [0, 0, ['min' => 0, 'max' => 10]],
            [1, 1, ['min' => 0, 'max' => 10]],
            [10, 10, ['min' => 0, 'max' => 10]],
            [12345, 10, ['min' => 0, 'max' => 10]],
            [-12345, 0, ['min' => 0, 'max' => 10]],

            [12345, 10, ['min' => -10, 'max' => 10]],
            [-12345, -10, ['min' => -10, 'max' => 10]],

            ['', 0],
            ['1', 1],
            ['0', 0],
            ['12345', 12345],
            ['-12345', -12345],
            ['123.45', 123],
            ['135.79', 135],
            ['-135.79', -135],
            ['test', 0],

            // Non-scalar values are not acceptable for this property
            // so default value should be set instead
            [[], 123, ['default' => 123]],
            [new \ArrayObject(), 123, ['default' => 123]],
            [new Property('abc'), 0],
            [new Property('12345'), 12345],
        ];
        return $tests;
    }
}
