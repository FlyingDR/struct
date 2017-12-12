<?php

namespace Flying\Tests\Metadata\Fixtures\Structs;

use Flying\Struct\Annotation as Struct;
use Flying\Tests\Metadata\Fixtures\Stubs\StructStub;

/**
 * @Struct\Struct(name="basic", class="BasicStruct", abc=123, xyz="test")
 * @Struct\Struct(name="child", class="StructWithChild")
 * @Struct\Struct(name="propertyTest", class="CustomPropertiesTest")
 * @Struct\Struct(name="annotationTest", class="CustomAnnotationsTest")
 */
class ComplexStructure extends StructStub implements MetadataTestcaseInterface
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
                'basic'          => [
                    'name'       => 'basic',
                    'class'      => BasicStruct::class,
                    'config'     => [
                        'abc' => 123,
                        'xyz' => 'test',
                    ],
                    'hash'       => 'test',
                    'properties' => [],
                ],
                'child'          => [
                    'name'       => 'child',
                    'class'      => StructWithChild::class,
                    'config'     => [],
                    'hash'       => 'test',
                    'properties' => [],
                ],
                'propertyTest'   => [
                    'name'       => 'propertyTest',
                    'class'      => CustomPropertiesTest::class,
                    'config'     => [],
                    'hash'       => 'test',
                    'properties' => [],
                ],
                'annotationTest' => [
                    'name'       => 'annotationTest',
                    'class'      => CustomAnnotationsTest::class,
                    'config'     => [],
                    'hash'       => 'test',
                    'properties' => [],
                ],
            ],
        ];
        foreach ((array)$metadata['properties'] as $name => $info) {
            /** @var $struct MetadataTestcaseInterface */
            $struct = new $info['class'];
            $meta = $struct->getExpectedMetadata();
            $meta['name'] = $info['name'];
            $metadata['properties'][$name]['properties'] = $meta['properties'];
        }
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
