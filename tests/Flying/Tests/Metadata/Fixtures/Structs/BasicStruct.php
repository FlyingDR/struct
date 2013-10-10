<?php

namespace Flying\Tests\Metadata\Fixtures\Structs;

use Flying\Struct\ConfigurationManager;
use Flying\Tests\Metadata\Fixtures\Stubs\StructStub;

/**
 * @Struct\Boolean(name="first", default=true)
 * @Struct\Int(name="second", nullable=false, default=100, min=10, max=1000)
 * @Struct\String(name="third")
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
        return array(
            'name'       => null,
            'class'      => __CLASS__,
            'config'     => array(),
            'hash'       => 'test',
            'properties' => array(
                'first'  => array(
                    'name'   => 'first',
                    'class'  => $pNs . '\\Boolean',
                    'config' => array(
                        'default' => true,
                    ),
                    'hash'   => 'test',
                ),
                'second' => array(
                    'name'   => 'second',
                    'class'  => $pNs . '\\Int',
                    'config' => array(
                        'nullable' => false,
                        'default'  => 100,
                        'min'      => 10,
                        'max'      => 1000,
                    ),
                    'hash'   => 'test',
                ),
                'third'  => array(
                    'name'   => 'third',
                    'class'  => $pNs . '\\String',
                    'config' => array(),
                    'hash'   => 'test',
                ),
                'fourth' => array(
                    'name'   => 'fourth',
                    'class'  => $pNs . '\\String',
                    'config' => array(
                        'default' => 'some value',
                    ),
                    'hash'   => 'test',
                ),
            ),
        );
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
