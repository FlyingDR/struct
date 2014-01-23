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
        $this->assertEquals($struct->getExpectedContents(), $struct->toArray());
    }

    public function testGettingProperty()
    {
        $struct = $this->getTestStruct();
        $this->assertInstanceOf('Flying\Struct\Property\PropertyInterface', $struct->getProperty('first'));
        $this->assertInstanceOf('Flying\Struct\Common\ComplexPropertyInterface', $struct->getProperty('collection'));
        $this->assertNull($struct->getProperty('unavailable'));
    }

    public function testRecursiveIteratorInterface()
    {
        $struct = $this->getTestStruct();
        $expected = array();
        $temp = $struct->getExpectedContents();
        // Don't walk recursively to avoid converting collection into list of its values
        array_walk(
            $temp,
            function ($v, $k) use (&$expected) {
                $expected[] = array($k, $v);
            }
        );
        $actual = array();
        $iterator = new \RecursiveIteratorIterator($struct);
        foreach ($iterator as $key => $value) {
            if ($value instanceof ComplexPropertyInterface) {
                $value = $value->toArray();
            }
            $actual[] = array($key, $value);
        }
        $this->assertEquals($expected, $actual);
    }

    public function testCollectionAccess()
    {
        $struct = $this->getTestStruct();
        $this->assertInstanceOf('Flying\Struct\Property\Collection', $struct->collection);
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
}
