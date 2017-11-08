<?php

namespace Flying\Tests\Metadata\Fixtures\Structs;

use Flying\Struct\ConfigurationManager;

/**
 * @Struct\Str(name="inherited", default="yes")
 */
class StructInheritedFromAbstract extends AbstractStruct
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
                'from_abstract' => array(
                    'name'   => 'from_abstract',
                    'class'  => $pNs . '\\Boolean',
                    'config' => array(
                        'default' => true,
                    ),
                    'hash'   => 'test',
                ),
                'inherited'     => array(
                    'name'   => 'inherited',
                    'class'  => $pNs . '\\Str',
                    'config' => array(
                        'default' => 'yes',
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
