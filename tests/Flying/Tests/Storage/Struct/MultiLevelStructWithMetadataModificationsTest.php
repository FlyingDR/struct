<?php

namespace Flying\Tests\Storage\Struct;

use Flying\Tests\Storage\Struct\Fixtures\MultiLevelStructWithMetadataModifications;
use Flying\Tests\Struct\Common\MultiLevelStructWithMetadataModificationsTest as CommonMultiLevelStructWithMetadataModificationsTest;

/**
 * @method MultiLevelStructWithMetadataModifications getTestStruct($contents = null, $config = null)
 */
class MultiLevelStructWithMetadataModificationsTest extends CommonMultiLevelStructWithMetadataModificationsTest
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
    protected $fixtureClass = 'Flying\Tests\Storage\Struct\Fixtures\MultiLevelStructWithMetadataModifications';
}
