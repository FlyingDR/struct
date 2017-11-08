<?php

namespace Flying\Tests\Metadata\Fixtures\Structs;

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
        $metadata = array(
            'name'       => null,
            'class'      => __CLASS__,
            'config'     => array(),
            'hash'       => 'test',
            'properties' => array(
                'boolean_property'                    => array(
                    'name'   => 'boolean_property',
                    'class'  => $pNs . '\\Boolean',
                    'config' => array(
                        'default' => true,
                    ),
                    'hash'   => 'test',
                ),
                'int_property'                        => array(
                    'name'   => 'int_property',
                    'class'  => $pNs . '\\Integer',
                    'config' => array(
                        'nullable' => false,
                        'default'  => 100,
                        'min'      => 10,
                        'max'      => 1000,
                    ),
                    'hash'   => 'test',
                ),
                'string_property'                     => array(
                    'name'   => 'string_property',
                    'class'  => $pNs . '\\Str',
                    'config' => array(),
                    'hash'   => 'test',
                ),
                'generic_property'                    => array(
                    'name'   => 'generic_property',
                    'class'  => $pNs . '\\Str',
                    'config' => array(
                        'default' => 'some value',
                    ),
                    'hash'   => 'test',
                ),
                'child_struct_with_explicit_class'    => array(
                    'name'   => 'child_struct_with_explicit_class',
                    'class'  => __NAMESPACE__ . '\\BasicStruct',
                    'config' => array(),
                    'hash'   => 'test',
                ),
                'child_struct_with_inline_definition' => array(
                    'name'       => 'child_struct_with_inline_definition',
                    'class'      => null,
                    'config'     => array(),
                    'hash'       => 'test',
                    'properties' => array(
                        'a' => array(
                            'name'   => 'a',
                            'class'  => $pNs . '\\Boolean',
                            'config' => array(),
                            'hash'   => 'test',
                        ),
                        'b' => array(
                            'name'   => 'b',
                            'class'  => $pNs . '\\Integer',
                            'config' => array(),
                            'hash'   => 'test',
                        ),
                        'c' => array(
                            'name'   => 'c',
                            'class'  => $pNs . '\\Str',
                            'config' => array(),
                            'hash'   => 'test',
                        ),
                        's' => array(
                            'name'       => 's',
                            'class'      => null,
                            'config'     => array(),
                            'hash'       => 'test',
                            'properties' => array(
                                'min' => array(
                                    'name'   => 'min',
                                    'class'  => $pNs . '\\Integer',
                                    'config' => array(
                                        'default' => 0,
                                    ),
                                    'hash'   => 'test',
                                ),
                                'max' => array(
                                    'name'   => 'max',
                                    'class'  => $pNs . '\\Integer',
                                    'config' => array(
                                        'default' => 100,
                                    ),
                                    'hash'   => 'test',
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        );
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
