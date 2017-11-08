<?php

namespace Flying\Tests\Property;

use Flying\Struct\Property\Property;
use Flying\Tests\Property\Stubs\ToArray;

class EnumTest extends BaseTypeTest
{
    /**
     * Class name of the property to test
     *
     * @var string
     */
    protected $propertyClass = 'Enum';
    /**
     * Default configuration options for test
     *
     * @var array
     */
    protected $defaultConfig = [
        'nullable' => false,
        'default'  => 'a',
        'values'   => ['a', 'b', 'c'],
    ];
    /**
     * Default property value
     *
     * @var string
     */
    protected $defaultValue = 'a';
    /**
     * Serialization tests
     *
     * @var array
     */
    protected $serializationTests = [
        'a',
    ];

    /**
     * {@inheritdoc}
     */
    public function getValueTests()
    {
        return [
            ['a', 'a', ['default' => 'b', 'values' => ['a', 'b', 'c']]],
            [null, 'b', ['default' => 'b', 'values' => ['a', 'b', 'c']]],
            ['x', 'b', ['default' => 'b', 'values' => ['a', 'b', 'c']]],
            [2, 2, ['default' => 3, 'values' => [1, 2, 3]]],
            ['2', 3, ['default' => 3, 'values' => [1, 2, 3]]],
            [[], 3, ['default' => 3, 'values' => [1, 2, 3]]],
            [new \ArrayObject(), 3, ['default' => 3, 'values' => [1, 2, 3]]],
            [new Property(1), 1, ['default' => 3, 'values' => [1, 2, 3]]],
            [1, 1, ['default' => 3, 'values' => new ToArray([1, 2, 3])]],
        ];
    }

    public function testAcceptableNullValue()
    {
        // This test is not needed for enum property
    }
}
