<?php

namespace Flying\Tests\Storage\Struct\Fixtures;

use Flying\Struct\Metadata\PropertyMetadata;
use Flying\Struct\Metadata\StructMetadata;
use Flying\Struct\Property\Str;

/**
 * @Struct\Boolean(name="first", default=true)
 * @Struct\Integer(name="second", nullable=false, default=100, min=10, max=1000)
 * @Struct\Str(name="third")
 * @Struct\Property(name="fourth", type="string", default="some value")
 */
class StructWithMetadataModifications extends TestStruct
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
        $property = new PropertyMetadata('new', Str::class, ['default' => 'custom property']);
        $metadata->addProperty($property);
    }

    /**
     * {@inheritdoc}
     */
    public function getExpectedContents()
    {
        return [
            'first'  => true,
            'second' => 100,
            'fourth' => 'another value',
            'new'    => 'custom property',
        ];
    }
}
