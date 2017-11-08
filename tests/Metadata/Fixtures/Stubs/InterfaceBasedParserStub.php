<?php

namespace Flying\Tests\Metadata\Fixtures\Stubs;

use Flying\Struct\Metadata\MetadataParserInterface;
use Flying\Struct\Metadata\StructMetadata;

class InterfaceBasedParserStub implements MetadataParserInterface
{
    /**
     * {@inheritdoc}
     */
    public function getMetadata($class)
    {
        return new StructMetadata();
    }
}
