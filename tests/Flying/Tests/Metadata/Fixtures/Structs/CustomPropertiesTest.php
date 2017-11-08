<?php

namespace Flying\Tests\Metadata\Fixtures\Structs;

use Flying\Struct\ConfigurationManager;
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
        return [
            'name'       => null,
            'class'      => __CLASS__,
            'config'     => [],
            'hash'       => 'test',
            'properties' => [
                'standard' => [
                    'name'   => 'standard',
                    'class'  => $nsMap->get('default') . '\\Str',
                    'config' => [],
                    'hash'   => 'test',
                ],
                'custom'   => [
                    'name'   => 'custom',
                    'class'  => $nsMap->get('fixtures') . '\\Custom',
                    'config' => [],
                    'hash'   => 'test',
                ],
                'complex'  => [
                    'name'   => 'complex',
                    'class'  => $nsMap->get('fixtures') . '\\PropertyWithComplexName',
                    'config' => [],
                    'hash'   => 'test',
                ],
            ],
        ];
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
