<?php

namespace Flying\Struct\Metadata;

use Flying\Struct\StructInterface;

/**
 * Interface for structures metadata management class
 */
interface MetadataManagerInterface
{

    /**
     * Get structure metadata information for given structure
     *
     * @param string|StructInterface $struct    Either structure class name or instance of structure object
     *                                          to get metadata for
     * @return StructMetadata|null
     */
    public function getMetadata($struct);

}
