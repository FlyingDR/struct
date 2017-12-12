<?php

namespace Flying\Tests\Metadata\Fixtures\Structs;

use Flying\Struct\Annotation as Struct;
use Flying\Struct\Metadata\MetadataModificationInterface;
use Flying\Struct\Metadata\StructMetadata;
use Flying\Struct\Property\Boolean;
use Flying\Struct\Property\Integer;
use Flying\Struct\Property\Str;
use Flying\Tests\Metadata\Fixtures\Stubs\StructStub;

/**
 * @Struct\Boolean(name="first", default=true)
 * @Struct\Integer(name="second", nullable=false, default=100, min=10, max=1000)
 * @Struct\Str(name="third")
 * @Struct\Property(name="fourth", type="string", default="some value")
 */
class StructWithMetadataModifications extends StructStub implements MetadataTestcaseInterface, MetadataModificationInterface
{
    /**
     * {@inheritdoc}
     */
    public static function modifyMetadata(StructMetadata $metadata)
    {
        $metadata->removeProperty('third');
        $fourth = $metadata->getProperty('fourth');
        $config = $fourth->getConfig();
        $config['default'] = 'another value';
        $fourth->setConfig($config);
    }

    /**
     * Get array representation of expected results from parsing metadata of this class
     *
     * @return array
     */
    public function getExpectedMetadata()
    {
        return [
            'name'       => null,
            'class'      => __CLASS__,
            'config'     => [],
            'hash'       => 'test',
            'properties' => [
                'first'  => [
                    'name'   => 'first',
                    'class'  => Boolean::class,
                    'config' => [
                        'default' => true,
                    ],
                    'hash'   => 'test',
                ],
                'second' => [
                    'name'   => 'second',
                    'class'  => Integer::class,
                    'config' => [
                        'nullable' => false,
                        'default'  => 100,
                        'min'      => 10,
                        'max'      => 1000,
                    ],
                    'hash'   => 'test',
                ],
                'fourth' => [
                    'name'   => 'fourth',
                    'class'  => Str::class,
                    'config' => [
                        'default' => 'another value',
                    ],
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
