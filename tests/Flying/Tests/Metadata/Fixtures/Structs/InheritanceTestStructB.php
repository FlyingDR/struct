<?php

namespace Flying\Tests\Metadata\Fixtures\Structs;

use Flying\Struct\ConfigurationManager;

/**
 * @Struct\Integer(name="a2")
 * @Struct\Boolean(name="b2")
 * @Struct\Str(name="c2")
 * @Struct\Str(name="overloaded", default="FromB")
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
        $metadata['properties']['a2'] = [
            'name'   => 'a2',
            'class'  => $pNs . '\\Integer',
            'config' => [],
            'hash'   => 'test',
        ];
        $metadata['properties']['b2'] = [
            'name'   => 'b2',
            'class'  => $pNs . '\\Boolean',
            'config' => [],
            'hash'   => 'test',
        ];
        $metadata['properties']['c2'] = [
            'name'   => 'c2',
            'class'  => $pNs . '\\Str',
            'config' => [],
            'hash'   => 'test',
        ];
        $metadata['properties']['overloaded'] = [
            'name'   => 'overloaded',
            'class'  => $pNs . '\\Str',
            'config' => [
                'default' => 'FromB',
            ],
            'hash'   => 'test',
        ];
        return $metadata;
    }
}
