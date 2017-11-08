<?php

namespace Flying\Tests\Storage\Struct;

use Flying\Tests\Storage\Struct\Fixtures\StructWithCollection;
use Flying\Tests\Struct\Common\StructWithCollectionTest as CommonStructWithCollectionTest;

class StructWithCollectionTest extends CommonStructWithCollectionTest
{
    /**
     * Namespace for fixtures structures
     *
     * @var string
     */
    protected $fixturesNs = 'Flying\Tests\Storage\Struct\Fixtures';
    /**
     * Name of fixture class to test
     *
     * @var string
     */
    protected $fixtureClass = StructWithCollection::class;
}
