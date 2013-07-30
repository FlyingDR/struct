<?php

namespace Flying\Tests\Metadata\Fixtures\Structs;

use Flying\Struct\ConfigurationManager;
use Flying\Tests\Metadata\Fixtures\Structs\MetadataTestcaseInterface;
use Flying\Tests\Metadata\Fixtures\Stubs\StructStub;

/**
 * @Struct\Property(name="standard", type="string")
 * @Struct\Property(name="custom", type="custom")
 * @Struct\Property(name="complex", type="propertyWithComplexName")
 */
class CustomPropertiesTest extends StructStub implements MetadataTestcaseInterface
{

    /**
     * Get array representation of expected results from parsing metadata of this class
     *
     * @return array
     */
    public function getExpectedMetadata()
    {
        $nsMap = ConfigurationManager::getConfiguration()->getPropertyNamespacesMap();
        return array(
            'name'       => null,
            'class'      => __CLASS__,
            'config'     => array(),
            'hash'       => 'test',
            'properties' => array(
                'standard' => array(
                    'name'   => 'standard',
                    'class'  => $nsMap->get('default') . '\\String',
                    'config' => array(),
                    'hash'   => 'test',
                ),
                'custom'   => array(
                    'name'   => 'custom',
                    'class'  => $nsMap->get('fixtures') . '\\Custom',
                    'config' => array(),
                    'hash'   => 'test',
                ),
                'complex'  => array(
                    'name'   => 'complex',
                    'class'  => $nsMap->get('fixtures') . '\\PropertyWithComplexName',
                    'config' => array(),
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
