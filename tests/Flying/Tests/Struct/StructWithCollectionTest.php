<?php

namespace Flying\Tests\Struct;

use Flying\Tests\Struct\Common\StructWithCollectionTest as CommonStructWithCollectionTest;
use Flying\Tests\Struct\Fixtures\StructWithClassValidatorCollection;

class StructWithCollectionTest extends CommonStructWithCollectionTest
{
    public function testCollectionWithClassValidation()
    {
        $struct = new StructWithClassValidatorCollection();
        static::assertEquals(0, $struct->collection->count());
        $struct->collection->add(new \ArrayObject());
        static::assertEquals(0, $struct->collection->count());
        $struct->collection->add(new \DateTime());
        static::assertEquals(1, $struct->collection->count());
    }
}
