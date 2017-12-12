<?php

namespace Flying\Tests\Metadata\Fixtures\Structs;

use Flying\Struct\Annotation as Struct;
use Flying\Struct\Property\Str;
use Flying\Tests\Metadata\Fixtures\Stubs\StructStub;

/**
 * @Struct\Str(name="name", nullable=false, maxlength=100)
 * @Struct\Str(name="email", nullable=false, maxlength=255)
 * @Struct\Struct(name="child", class="BasicStruct", option="value", another=12345)
 */
class StructWithChild extends StructStub implements MetadataTestcaseInterface
{
    /**
     * Get array representation of expected results from parsing metadata of this class
     *
     * @return array
     */
    public function getExpectedMetadata()
    {
        $metadata = [
            'name'       => null,
            'class'      => __CLASS__,
            'config'     => [],
            'hash'       => 'test',
            'properties' => [
                'name'  => [
                    'name'   => 'name',
                    'class'  => Str::class,
                    'config' => [
                        'nullable'  => false,
                        'maxlength' => 100,
                    ],
                    'hash'   => 'test',
                ],
                'email' => [
                    'name'   => 'email',
                    'class'  => Str::class,
                    'config' => [
                        'nullable'  => false,
                        'maxlength' => 255,
                    ],
                    'hash'   => 'test',
                ],
                'child' => [
                    'name'       => 'child',
                    'class'      => BasicStruct::class,
                    'config'     => [
                        'option'  => 'value',
                        'another' => 12345,
                    ],
                    'hash'       => 'test',
                    'properties' => [],
                ],
            ],
        ];
        /** @var $child MetadataTestcaseInterface */
        $child = new $metadata['properties']['child']['class'];
        $meta = $child->getExpectedMetadata();
        $metadata['properties']['child']['properties'] = $meta['properties'];
        return $metadata;
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
