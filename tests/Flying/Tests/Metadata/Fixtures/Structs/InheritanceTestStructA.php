<?php

namespace Flying\Tests\Metadata\Fixtures\Structs;

use Flying\Struct\ConfigurationManager;
use Flying\Tests\Metadata\Fixtures\Structs\MetadataTestcaseInterface;
use Flying\Tests\Metadata\Fixtures\Stubs\StructStub;

/**
 * @Struct\Int(name="a1")
 * @Struct\Boolean(name="b1")
 * @Struct\String(name="c1")
 * @Struct\String(name="overloaded", default="FromA", nullable=false)
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
        return array(
            'name'       => null,
            'class'      => __CLASS__,
            'config'     => array(),
            'properties' => array(
                'a1'         => array(
                    'name'   => 'a1',
                    'class'  => $pNs . '\\Int',
                    'config' => array(),
                ),
                'b1'         => array(
                    'name'   => 'b1',
                    'class'  => $pNs . '\\Boolean',
                    'config' => array(),
                ),
                'c1'         => array(
                    'name'   => 'c1',
                    'class'  => $pNs . '\\String',
                    'config' => array(),
                ),
                'overloaded' => array(
                    'name'   => 'overloaded',
                    'class'  => $pNs . '\\String',
                    'config' => array(
                        'default' => 'FromA',
                    ),
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
