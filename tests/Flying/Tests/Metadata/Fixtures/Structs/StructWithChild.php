<?php

namespace Flying\Tests\Metadata\Fixtures\Structs;

use Flying\Struct\ConfigurationManager;
use Flying\Tests\Metadata\Fixtures\Structs\MetadataTestcaseInterface;
use Flying\Tests\Metadata\Fixtures\Stubs\StructStub;

/**
 * @Struct\String(name="name", nullable=false, maxlength=100)
 * @Struct\String(name="email", nullable=false, maxlength=255)
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
        $pNs = ConfigurationManager::getConfiguration()->getPropertyNamespacesMap()->get('default');
        $metadata = array(
            'name'       => null,
            'class'      => __CLASS__,
            'config'     => array(),
            'properties' => array(
                'name'  => array(
                    'name'   => 'name',
                    'class'  => $pNs . '\\String',
                    'config' => array(
                        'nullable'  => false,
                        'maxlength' => 100,
                    ),
                ),
                'email' => array(
                    'name'   => 'email',
                    'class'  => $pNs . '\\String',
                    'config' => array(
                        'nullable'  => false,
                        'maxlength' => 255,
                    ),
                ),
                'child' => array(
                    'name'       => 'child',
                    'class'      => __NAMESPACE__ . '\\BasicStruct',
                    'config'     => array(
                        'option'  => 'value',
                        'another' => 12345,
                    ),
                    'properties' => array(),
                ),
            ),
        );
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