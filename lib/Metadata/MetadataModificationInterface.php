<?php

namespace Flying\Struct\Metadata;

/**
 * Interface for structure classes that allow modifications of their metadata
 */
interface MetadataModificationInterface
{
    /**
     * Modify metadata for this structure after it was parsed by metadata parser
     *
     * @param StructMetadata $metadata
     */
    public static function modifyMetadata(StructMetadata $metadata);
}
