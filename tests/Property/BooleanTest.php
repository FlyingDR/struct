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
    protected $serializationTests = [
        true,
        false,
    ];

    /**
     * {@inheritdoc}
     */
    public function getValueTests()
    {
        return [
            [true, true],
            [false, false],
            [1, true],
            [0, false],
            [-1, true],
            ['', false],
            ['0', false],
            ['test', true],
            [[], false],
            [[1, 2, 3], true],
            [new \ArrayObject(), true],
            [new Property(false), false],
        ];
    }
}
