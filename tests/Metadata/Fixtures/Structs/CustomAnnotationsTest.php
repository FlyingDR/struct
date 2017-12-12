<?php

namespace Flying\Tests\Metadata\Fixtures\Structs;

use Flying\Struct\Annotation as Struct;
use Flying\Struct\Property\Str;
use Flying\Tests\Metadata\Fixtures\Annotations as CustomStruct;
use Flying\Tests\Metadata\Fixtures\Properties\Custom;
use Flying\Tests\Metadata\Fixtures\Stubs\StructStub;

/**
 * @Struct\Property(name="standard", type="string")
 * @Struct\Property(name="custom", type="custom", test=123, enabled=true)
 * @CustomStruct\Custom(name="fromAnnotation", abc="xyz")
 */
class CustomAnnotationsTest extends StructStub implements MetadataTestcaseInterface
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
                'standard'       => [
                    'name'   => 'standard',
                    'class'  => Str::class,
                    'config' => [],
                    'hash'   => 'test',
                ],
                'custom'         => [
                    'name'   => 'custom',
                    'class'  => Custom::class,
                    'config' => [
                        'test'    => 123,
                        'enabled' => true,
                    ],
                    'hash'   => 'test',
                ],
                'fromAnnotation' => [
                    'name'   => 'fromAnnotation',
                    'class'  => Custom::class,
                    'config' => [
                        'abc' => 'xyz',
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
