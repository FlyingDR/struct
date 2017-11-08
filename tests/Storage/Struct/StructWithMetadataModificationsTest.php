<?php

namespace Flying\Tests\Storage\Struct;

use Flying\Tests\Storage\Struct\Fixtures\StructWithMetadataModifications;
use Flying\Tests\Struct\Common\StructWithMetadataModificationsTest as CommonStructWithMetadataModificationsTest;

class StructWithMetadataModificationsTest extends CommonStructWithMetadataModificationsTest
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
    protected $fixtureClass = StructWithMetadataModifications::class;
}
