<?php

namespace Flying\Tests\Struct\Fixtures;

/**
 * Fixture class with inline structure definition
 *
 * @Struct\Boolean(name="boolean_property", default=true),
 * @Struct\Int(name="int_property", nullable=false, default=100, min=10, max=1000),
 * @Struct\String(name="string_property"),
 * @Struct\Property(name="generic_property", type="string", default="some value"),
 * @Struct\Struct(name="child_struct_with_explicit_class", class="BasicStruct"),
 * @Struct\Struct(name="child_struct_with_inline_definition", {
 *      @Struct\Boolean(name="a"),
 *      @Struct\Int(name="b"),
 *      @Struct\String(name="c"),
 *      @Struct\Struct(name="s", {
 *          @Struct\Int(name="min", default=0),
 *          @Struct\Int(name="max", default=100)
 *     })
 * })
 */
class InlineStructDefinition extends TestStruct
{

    /**
     * {@inheritdoc}
     */
    public function getExpectedContents()
    {
        $contents = array(
            'boolean_property'                    => true,
            'int_property'                        => 100,
            'string_property'                     => null,
            'generic_property'                    => 'some value',
            'child_struct_with_explicit_class'    => null,
            'child_struct_with_inline_definition' => array(
                'a' => null,
                'b' => null,
                'c' => null,
                's' => array(
                    'min' => 0,
                    'max' => 100,
                ),
            ),
        );
        $child = new BasicStruct();
        $contents['child_struct_with_explicit_class'] = $child->getExpectedContents();
        return $contents;
    }
}
