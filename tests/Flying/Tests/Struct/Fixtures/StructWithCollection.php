<?php

namespace Flying\Tests\Struct\Fixtures;

use Flying\Struct\Property\Collection;

/**
 * Test structure with collection property
 *
 * @property Collection $collection
 * @Struct\Collection(name="collection", default={1,2,3}, allowed={1,2,3,4,5})
 */
class StructWithCollection extends BasicStruct
{

    /**
     * {@inheritdoc}
     */
    public function getExpectedContents()
    {
        $contents = parent::getExpectedContents();
        $contents['collection'] = array(1, 2, 3);
        return $contents;
    }

}
