<?php

namespace Flying\Tests\Metadata\Fixtures\Stubs;

use Flying\Struct\Metadata\AbstractMetadataParser;
use Flying\Struct\Metadata\StructMetadata;

class AbstractParserBasedParserStub extends AbstractMetadataParser
{
    /**
     * {@inheritdoc}
     */
    protected function parseMetadata(\ReflectionClass $reflection, StructMetadata $metadata)
    {
    }
}
