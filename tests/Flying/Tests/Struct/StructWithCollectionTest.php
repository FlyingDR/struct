<?php

namespace Flying\Tests\Struct;

use Flying\Tests\Struct\Common\StructWithCollectionTest as CommonStructWithCollectionTest;
use Flying\Tests\Struct\Fixtures\StructWithClassValidatorCollection;

class StructWithCollectionTest extends CommonStructWithCollectionTest
{
    public function testCollectionWithClassValidation()
    {
        $struct = new StructWithClassValidatorCollection();
        $this->assertEquals(0, $struct->collection->count());
        $struct->collection->add(new \ArrayObject());
        $this->assertEquals(0, $struct->collection->count());
        $struct->collection->add(new \DateTime());
        $this->assertEquals(1, $struct->collection->count());
    }
}
