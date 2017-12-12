<?php

namespace Flying\Tests\Struct\Fixtures;

use Flying\Struct\Annotation as Struct;
use Flying\Struct\Property\Collection;

/**
 * Structure to test collection validation
 *
 * @property Collection $collection
 * @Struct\Collection(name="collection", allowed="\DateTime")
 */
class StructWithClassValidatorCollection extends BasicStruct
{
    /**
     * {@inheritdoc}
     */
    public function getExpectedContents()
    {
        $contents = parent::getExpectedContents();
        $contents['collection'] = [];
        return $contents;
    }
}
