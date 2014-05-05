<?php

namespace Flying\Tests\Storage\Struct;

use Flying\Tests\Storage\Struct\Fixtures\StructWithMetadataModifications;
use Flying\Tests\Struct\Common\StructWithMetadataModificationsTest as CommonStructWithMetadataModificationsTest;

/**
 * @method StructWithMetadataModifications getTestStruct($contents = null, $config = null)
 */
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
    protected $fixtureClass = 'Flying\Tests\Storage\Struct\Fixtures\StructWithMetadataModifications';
}
