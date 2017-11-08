<?php

namespace Flying\Struct\Metadata;

/**
 * Interface for implementing structures metadata parsers
 */
interface MetadataParserInterface
{
    /**
     * Get structure metadata information for given class
     *
     * @param string $class Structure class name to get metadata for
     * @return StructMetadata
     */
    public function getMetadata($class);
}
