<?php

namespace Flying\Tests\Storage\Struct;

use Flying\Tests\Struct\Common\InlineStructDefinitionTest as CommonInlineStructDefinitionTest;

/**
 * @method \Flying\Tests\Storage\Struct\Fixtures\InlineStructDefinition getTestStruct($contents = null, $config = null)
 */
class InlineStructDefinitionTest extends CommonInlineStructDefinitionTest
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
    protected $fixtureClass = 'Flying\Tests\Storage\Struct\Fixtures\InlineStructDefinition';
}
