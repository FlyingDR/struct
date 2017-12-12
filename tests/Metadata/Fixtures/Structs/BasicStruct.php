<?php

namespace Flying\Tests\Metadata\Fixtures\Structs;

use Flying\Struct\Annotation as Struct;
use Flying\Struct\ConfigurationManager;
use Flying\Tests\Metadata\Fixtures\Stubs\StructStub;

/**
 * @Struct\Boolean(name="first", default=true)
 * @Struct\Integer(name="second", nullable=false, default=100, min=10, max=1000)
 * @Struct\Str(name="third")
 * @Struct\Property(name="fourth", type="string", default="some value")
 */
class BasicStruct extends StructStub implements MetadataTestcaseInterface
{
    /**
     * Get array representation of expected results from parsing metadata of this class
     *
     * @return array
     */
    public function getExpectedMetadata()
    {
        $pNs = ConfigurationManager::getConfiguration()->getPropertyNamespacesMap()->get('default');
        return [
            'name'       => null,
            'class'      => __CLASS__,
            'config'     => [],
            'hash'       => 'test',
            'properties' => [
                'first'  => [
                    'name'   => 'first',
                    'class'  => $pNs . '\\Boolean',
                    'config' => [
                        'default' => true,
                    ],
                    'hash'   => 'test',
                ],
                'second' => [
                    'name'   => 'second',
                    'class'  => $pNs . '\\Integer',
                    'config' => [
                        'nullable' => false,
                        'default'  => 100,
                        'min'      => 10,
                        'max'      => 1000,
                    ],
                    'hash'   => 'test',
                ],
                'third'  => [
                    'name'   => 'third',
                    'class'  => $pNs . '\\Str',
                    'config' => [],
                    'hash'   => 'test',
                ],
                'fourth' => [
                    'name'   => 'fourth',
                    'class'  => $pNs . '\\Str',
                    'config' => [
                        'default' => 'some value',
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
