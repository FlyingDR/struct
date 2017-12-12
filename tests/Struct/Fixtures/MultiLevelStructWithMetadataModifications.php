<?php

namespace Flying\Tests\Struct\Fixtures;

use Flying\Struct\Annotation as Struct;
use Flying\Struct\Metadata\StructMetadata;

/**
 * @Struct\Str(name="own", default="own property")
 * @Struct\Struct(name="child", class="BasicStruct")
 */
class MultiLevelStructWithMetadataModifications extends TestStruct
{
    /**
     * {@inheritdoc}
     */
    public static function modifyMetadata(StructMetadata $metadata)
    {
        /** @var $childMetadata StructMetadata */
        $childMetadata = $metadata->getProperty('child');
        $childMetadata->removeProperty('first');
        $property = $childMetadata->getProperty('fourth');
        $config = $property->getConfig();
        $config['default'] = 'modified value';
        $property->setConfig($config);
    }

    /**
     * {@inheritdoc}
     */
    public function getExpectedContents()
    {
        return [
            'own'   => 'own property',
            'child' => [
                'second' => 100,
                'third'  => null,
                'fourth' => 'modified value',
            ],
        ];
    }
}
