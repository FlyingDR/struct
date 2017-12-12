<?php

namespace Flying\Tests\Metadata\Fixtures\Structs;

use Flying\Struct\Annotation as Struct;
use Flying\Struct\Property\Boolean;
use Flying\Struct\Property\Integer;
use Flying\Struct\Property\Str;

/**
 * @Struct\Integer(name="a2")
 * @Struct\Boolean(name="b2")
 * @Struct\Str(name="c2")
 * @Struct\Str(name="overloaded", default="FromB")
 */
class InheritanceTestStructB extends InheritanceTestStructA
{
    /**
     * Get array representation of expected results from parsing metadata of this class
     *
     * @return array
     */
    public function getExpectedMetadata()
    {
        $metadata = parent::getExpectedMetadata();
        $metadata['class'] = __CLASS__;
        $metadata['properties']['a2'] = [
            'name'   => 'a2',
            'class'  => Integer::class,
            'config' => [],
            'hash'   => 'test',
        ];
        $metadata['properties']['b2'] = [
            'name'   => 'b2',
            'class'  => Boolean::class,
            'config' => [],
            'hash'   => 'test',
        ];
        $metadata['properties']['c2'] = [
            'name'   => 'c2',
            'class'  => Str::class,
            'config' => [],
            'hash'   => 'test',
        ];
        $metadata['properties']['overloaded'] = [
            'name'   => 'overloaded',
            'class'  => Str::class,
            'config' => [
                'default' => 'FromB',
            ],
            'hash'   => 'test',
        ];
        return $metadata;
    }
}
