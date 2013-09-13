<?php

namespace Flying\Tests\Metadata\Fixtures\Structs;

use Flying\Struct\ConfigurationManager;

/**
 * @Struct\Int(name="a2")
 * @Struct\Boolean(name="b2")
 * @Struct\String(name="c2")
 * @Struct\String(name="overloaded", default="FromB")
 */
class InheritanceTestStructB extends InheritanceTestStructA
{
    /**
     * Get array representation of expected results from parsing metadata of this class
     *
     * @return array
     */
    public function getExpectedMetadata()
    {
        $pNs = ConfigurationManager::getConfiguration()->getPropertyNamespacesMap()->get('default');
        $metadata = parent::getExpectedMetadata();
        $metadata['class'] = __CLASS__;
        $metadata['properties']['a2'] = array(
            'name'   => 'a2',
            'class'  => $pNs . '\\Int',
            'config' => array(),
            'hash'   => 'test',
        );
        $metadata['properties']['b2'] = array(
            'name'   => 'b2',
            'class'  => $pNs . '\\Boolean',
            'config' => array(),
            'hash'   => 'test',
        );
        $metadata['properties']['c2'] = array(
            'name'   => 'c2',
            'class'  => $pNs . '\\String',
            'config' => array(),
            'hash'   => 'test',
        );
        $metadata['properties']['overloaded'] = array(
            'name'   => 'overloaded',
            'class'  => $pNs . '\\String',
            'config' => array(
                'default' => 'FromB',
            ),
            'hash'   => 'test',
        );
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
