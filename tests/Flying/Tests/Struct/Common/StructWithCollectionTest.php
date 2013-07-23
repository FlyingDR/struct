<?php

namespace Flying\Tests\Struct\Common;

use Flying\Tests\Struct\Fixtures\StructWithCollection;

abstract class StructWithCollectionTest extends BaseStructTest
{
    public function testCreation()
    {
        $struct = $this->getTestStruct();
        $this->assertEquals($struct->getExpectedContents(), $struct->toArray());
    }

    public function testCollectionAccess()
    {
        $struct = $this->getTestStruct();
        $this->assertInstanceOf('\Flying\Struct\Property\Collection', $struct->collection);
    }

    public function testArrayAccess()
    {
        $struct = $this->getTestStruct();
        $struct->collection[] = 4;
        $struct->collection[] = 5;
        $struct->collection[] = 6;
        $struct->collection[] = 7;
        /** @noinspection PhpUndefinedMethodInspection */
        $this->assertEquals(array(1, 2, 3, 4, 5), $struct->collection->toArray());
    }

    /**
     * @param array|object $contents    OPTIONAL Contents to initialize structure with
     * @param array|object $config      OPTIONAL Configuration for this structure
     * @return StructWithCollection
     */
    protected function getTestStruct($contents = null, $config = null)
    {
        $class = $this->getFixtureClass('StructWithCollection');
        $struct = new $class($contents, $config);
        return $struct;
    }

}
