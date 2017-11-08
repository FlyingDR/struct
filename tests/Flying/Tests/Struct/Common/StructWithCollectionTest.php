<?php

namespace Flying\Tests\Struct\Common;

use Flying\Struct\Common\ComplexPropertyInterface;
use Flying\Tests\Struct\Fixtures\StructWithCollection;

/**
 * @method StructWithCollection getTestStruct($contents = null, $config = null)
 */
abstract class StructWithCollectionTest extends BaseStructTest
{
    /**
     * Name of fixture class to test
     *
     * @var string
     */
    protected $fixtureClass = 'Flying\Tests\Struct\Fixtures\StructWithCollection';

    public function testCreation()
    {
        $struct = $this->getTestStruct();
        static::assertEquals($struct->getExpectedContents(), $struct->toArray());
    }

    public function testGettingProperty()
    {
        $struct = $this->getTestStruct();
        static::assertInstanceOf('Flying\Struct\Property\PropertyInterface', $struct->getProperty('first'));
        static::assertInstanceOf('Flying\Struct\Common\ComplexPropertyInterface', $struct->getProperty('collection'));
        static::assertNull($struct->getProperty('unavailable'));
    }

    public function testRecursiveIteratorInterface()
    {
        $struct = $this->getTestStruct();
        $expected = [];
        $temp = $struct->getExpectedContents();
        // Don't walk recursively to avoid converting collection into list of its values
        array_walk(
            $temp,
            function ($v, $k) use (&$expected) {
                $expected[] = [$k, $v];
            }
        );
        $actual = [];
        $iterator = new \RecursiveIteratorIterator($struct);
        foreach ($iterator as $key => $value) {
            if ($value instanceof ComplexPropertyInterface) {
                $value = $value->toArray();
            }
            $actual[] = [$key, $value];
        }
        static::assertEquals($expected, $actual);
    }

    public function testCollectionAccess()
    {
        $struct = $this->getTestStruct();
        static::assertInstanceOf('Flying\Struct\Property\Collection', $struct->collection);
    }

    public function testArrayAccess()
    {
        $struct = $this->getTestStruct();
        $struct->collection[] = 4;
        $struct->collection[] = 5;
        $struct->collection[] = 6;
        $struct->collection[] = 7;
        static::assertEquals([1, 2, 3, 4, 5], $struct->collection->toArray());
    }
}
