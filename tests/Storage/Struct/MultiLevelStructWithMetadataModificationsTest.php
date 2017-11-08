<?php

namespace Flying\Tests\Storage\Struct;

use Flying\Tests\Storage\Struct\Fixtures\MultiLevelStructWithMetadataModifications;
use Flying\Tests\Struct\Common\MultiLevelStructWithMetadataModificationsTest as CommonMultiLevelStructWithMetadataModificationsTest;

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
    protected $fixtureClass = MultiLevelStructWithMetadataModifications::class;
}
