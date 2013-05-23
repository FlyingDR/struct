<?php

namespace Flying\Tests\Metadata\Fixtures\Structs;

use Flying\Struct\ConfigurationManager;
use Flying\Tests\Metadata\Fixtures\Structs\MetadataTestcaseInterface;
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
        $nsMap = ConfigurationManager::getConfiguration()->getStructNamespacesMap();
        $metadata = array(
            'name'       => null,
            'class'      => __CLASS__,
            'config'     => array(),
            'properties' => array(
                'basic'          => array(
                    'name'       => 'basic',
                    'class'      => $nsMap->get('fixtures') . '\\BasicStruct',
                    'config'     => array(
                        'abc' => 123,
                        'xyz' => 'test',
                    ),
                    'properties' => array(),
                ),
                'child'          => array(
                    'name'       => 'child',
                    'class'      => $nsMap->get('fixtures') . '\\StructWithChild',
                    'config'     => array(),
                    'properties' => array(),
                ),
                'propertyTest'   => array(
                    'name'       => 'propertyTest',
                    'class'      => $nsMap->get('fixtures') . '\\CustomPropertiesTest',
                    'config'     => array(),
                    'properties' => array(),
                ),
                'annotationTest' => array(
                    'name'       => 'annotationTest',
                    'class'      => $nsMap->get('fixtures') . '\\CustomAnnotationsTest',
                    'config'     => array(),
                    'properties' => array(),
                ),
            ),
        );
        foreach ($metadata['properties'] as $name => $info) {
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
