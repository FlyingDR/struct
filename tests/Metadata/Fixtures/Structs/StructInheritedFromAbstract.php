<?php

namespace Flying\Tests\Metadata\Fixtures\Structs;

use Flying\Struct\Annotation as Struct;
use Flying\Struct\Property\Boolean;
use Flying\Struct\Property\Str;

/**
 * @Struct\Str(name="inherited", default="yes")
 */
class StructInheritedFromAbstract extends AbstractStruct
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
                'from_abstract' => [
                    'name'   => 'from_abstract',
                    'class'  => Boolean::class,
                    'config' => [
                        'default' => true,
                    ],
                    'hash'   => 'test',
                ],
                'inherited'     => [
                    'name'   => 'inherited',
                    'class'  => Str::class,
                    'config' => [
                        'default' => 'yes',
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
