<?php

namespace Flying\Tests\Metadata\Fixtures\Structs;

use Flying\Struct\ConfigurationManager;
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
        $pNs = ConfigurationManager::getConfiguration()->getPropertyNamespacesMap()->get('default');
        return [
            'name'       => null,
            'class'      => __CLASS__,
            'config'     => [],
            'hash'       => 'test',
            'properties' => [
                'a1'         => [
                    'name'   => 'a1',
                    'class'  => $pNs . '\\Integer',
                    'config' => [],
                    'hash'   => 'test',
                ],
                'b1'         => [
                    'name'   => 'b1',
                    'class'  => $pNs . '\\Boolean',
                    'config' => [],
                    'hash'   => 'test',
                ],
                'c1'         => [
                    'name'   => 'c1',
                    'class'  => $pNs . '\\Str',
                    'config' => [],
                    'hash'   => 'test',
                ],
                'overloaded' => [
                    'name'   => 'overloaded',
                    'class'  => $pNs . '\\Str',
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
