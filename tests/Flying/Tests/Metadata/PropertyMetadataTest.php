<?php

namespace Flying\Tests\Metadata;

use Flying\Struct\Metadata\PropertyMetadata;

class PropertyMetadataTest extends BaseMetadataTest
{

    /**
     * @return PropertyMetadata
     */
    protected function getMetadataObject()
    {
        return new PropertyMetadata();
    }

}
