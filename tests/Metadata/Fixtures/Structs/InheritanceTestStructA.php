<?php

namespace Flying\Tests\Metadata\Fixtures\Structs;

use Flying\Struct\Annotation as Struct;
use Flying\Struct\Property\Boolean;
use Flying\Struct\Property\Integer;
use Flying\Struct\Property\Str;
use Flying\Tests\Metadata\Fixtures\Stubs\StructStub;

/**
 * @Struct\Integer(name="a1")
 * @Struct\Boolean(name="b1")
 * @Struct\Str(name="c1")
 * @Struct\Str(name="overloaded", default="FromA", nullable=false)
 */
class InheritanceTestStructA extends StructStub implements MetadataTestcaseInterface
{
    /**
     * Get array representation of expected results from parsing metadata of this class
     *
     * @return array
     */
    public function getExpectedMetadata()
    {
        return [
            'name'       => null,
            'class'      => __CLASS__,
            'config'     => [],
            'hash'       => 'test',
            'properties' => [
                'a1'         => [
                    'name'   => 'a1',
                    'class'  => Integer::class,
                    'config' => [],
                    'hash'   => 'test',
                ],
                'b1'         => [
                    'name'   => 'b1',
                    'class'  => Boolean::class,
                    'config' => [],
                    'hash'   => 'test',
                ],
                'c1'         => [
                    'name'   => 'c1',
                    'class'  => Str::class,
                    'config' => [],
                    'hash'   => 'test',
                ],
                'overloaded' => [
                    'name'   => 'overloaded',
                    'class'  => Str::class,
                    'config' => [
                        'default' => 'FromA',
                    ],
                    'hash'   => 'test',
                ],
            ],
        ];
    }

    /**
     * Get expected exception that should be raised when parsing metadata from this testcase
     *
     * @return string|array|null
     */
    public function getExpectedException()
    {
        return null;
    }
}
