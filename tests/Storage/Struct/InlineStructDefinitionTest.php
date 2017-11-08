<?php

namespace Flying\Tests\Storage\Struct;

use Flying\Tests\Storage\Struct\Fixtures\InlineStructDefinition;
use Flying\Tests\Struct\Common\InlineStructDefinitionTest as CommonInlineStructDefinitionTest;

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
    protected $fixtureClass = InlineStructDefinition::class;
}
