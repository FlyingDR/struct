<?php

namespace Flying\Tests\Struct\Common;

use Flying\Tests\Struct\Fixtures\MultiLevelStructWithMetadataModifications;

/**
 * @method MultiLevelStructWithMetadataModifications getTestStruct($contents = null, $config = null)
 */
class MultiLevelStructWithMetadataModificationsTest extends BaseStructTest
{
    /**
     * Name of fixture class to test
     *
     * @var string
     */
    protected $fixtureClass = MultiLevelStructWithMetadataModifications::class;
}
