<?php

namespace Flying\Tests\Metadata\Fixtures\Structs;

use Flying\Struct\Annotation as Struct;
use Flying\Struct\ConfigurationManager;
use Flying\Tests\Metadata\Fixtures\Stubs\StructStub;

/**
 * Fixture class with inline structure definition
 *
 * @Struct\Boolean(name="boolean_property", default=true),
 * @Struct\Integer(name="int_property", nullable=false, default=100, min=10, max=1000),
 * @Struct\Str(name="string_property"),
 * @Struct\Property(name="generic_property", type="string", default="some value"),
 * @Struct\Struct(name="child_struct_with_explicit_class", class="BasicStruct"),
 * @Struct\Struct(name="child_struct_with_inline_definition", {
 *      @Struct\Boolean(name="a"),
 *      @Struct\Integer(name="b"),
 *      @Struct\Str(name="c"),
 *      @Struct\Struct(name="s", {
 *          @Struct\Integer(name="min", default=0),
 *          @Struct\Integer(name="max", default=100)
 *     })
 * })
 */
class InlineStructDefinition extends StructStub implements MetadataTestcaseInterface
{
    /**
     * Get array representation of expected results from parsing metadata of this class
     *
     * @return array
     */
    public function getExpectedMetadata()
    {
        $pNs = ConfigurationManager::getConfiguration()->getPropertyNamespacesMap()->get('default');
        $metadata = [
            'name'       => null,
            'class'      => __CLASS__,
            'config'     => [],
            'hash'       => 'test',
            'properties' => [
                'boolean_property'                    => [
                    'name'   => 'boolean_property',
                    'class'  => $pNs . '\\Boolean',
                    'config' => [
                        'default' => true,
                    ],
                    'hash'   => 'test',
                ],
                'int_property'                        => [
                    'name'   => 'int_property',
                    'class'  => $pNs . '\\Integer',
                    'config' => [
                        'nullable' => false,
                        'default'  => 100,
                        'min'      => 10,
                        'max'      => 1000,
                    ],
                    'hash'   => 'test',
                ],
                'string_property'                     => [
                    'name'   => 'string_property',
                    'class'  => $pNs . '\\Str',
                    'config' => [],
                    'hash'   => 'test',
                ],
                'generic_property'                    => [
                    'name'   => 'generic_property',
                    'class'  => $pNs . '\\Str',
                    'config' => [
                        'default' => 'some value',
                    ],
                    'hash'   => 'test',
                ],
                'child_struct_with_explicit_class'    => [
                    'name'   => 'child_struct_with_explicit_class',
                    'class'  => __NAMESPACE__ . '\\BasicStruct',
                    'config' => [],
                    'hash'   => 'test',
                ],
                'child_struct_with_inline_definition' => [
                    'name'       => 'child_struct_with_inline_definition',
                    'class'      => null,
                    'config'     => [],
                    'hash'       => 'test',
                    'properties' => [
                        'a' => [
                            'name'   => 'a',
                            'class'  => $pNs . '\\Boolean',
                            'config' => [],
                            'hash'   => 'test',
                        ],
                        'b' => [
                            'name'   => 'b',
                            'class'  => $pNs . '\\Integer',
                            'config' => [],
                            'hash'   => 'test',
                        ],
                        'c' => [
                            'name'   => 'c',
                            'class'  => $pNs . '\\Str',
                            'config' => [],
                            'hash'   => 'test',
                        ],
                        's' => [
                            'name'       => 's',
                            'class'      => null,
                            'config'     => [],
                            'hash'       => 'test',
                            'properties' => [
                                'min' => [
                                    'name'   => 'min',
                                    'class'  => $pNs . '\\Integer',
                                    'config' => [
                                        'default' => 0,
                                    ],
                                    'hash'   => 'test',
                                ],
                                'max' => [
                                    'name'   => 'max',
                                    'class'  => $pNs . '\\Integer',
                                    'config' => [
                                        'default' => 100,
                                    ],
                                    'hash'   => 'test',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        /** @var $child MetadataTestcaseInterface */
        $child = new $metadata['properties']['child_struct_with_explicit_class']['class'];
        $meta = $child->getExpectedMetadata();
        $metadata['properties']['child_struct_with_explicit_class']['properties'] = $meta['properties'];
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
