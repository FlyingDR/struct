<?php

namespace Flying\Tests\Struct\Common;

use Flying\Tests\Struct\Fixtures\StructWithMetadataModifications;

/**
 * @method StructWithMetadataModifications getTestStruct($contents = null, $config = null)
 */
class StructWithMetadataModificationsTest extends BaseStructTest
{
    /**
     * Name of fixture class to test
     *
     * @var string
     */
    protected $fixtureClass = 'Flying\Tests\Struct\Fixtures\StructWithMetadataModifications';
}
