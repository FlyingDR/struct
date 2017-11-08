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
    protected $serializationTests = [
        '',
        'some text',
        'some long text for property value',
    ];

    /**
     * {@inheritdoc}
     */
    public function getValueTests()
    {
        return [
            [true, '1'],
            [false, ''],

            [1, '1'],
            [0, '0'],
            [12345, '12345'],
            [-12345, '-12345'],
            [123.45, '123.45'],
            [-123.45, '-123.45'],

            ['test', 'test'],
            ['some long text for property value', 'some long text for property value'],
            ['some long text for property value', 'some long ', ['maxlength' => 10]],
            ['some long text for property value', 'some long text for p', ['maxlength' => 20]],

            [[], 'test', ['default' => 'test']],
            [new \ArrayObject(), 'test', ['default' => 'test']],
            [new Property('abc'), 'abc'],
            [new Property('12345'), '12345'],
            [new ToString('test string'), 'test string'],
            [new UsToString('another test string'), 'another test string'],
        ];
    }
}
