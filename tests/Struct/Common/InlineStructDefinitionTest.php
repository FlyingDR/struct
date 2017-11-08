<?php

namespace Flying\Tests\Struct\Common;

use Flying\Tests\Struct\Fixtures\InlineStructDefinition;

/**
 * @method InlineStructDefinition getTestStruct($contents = null, $config = null)
 */
abstract class InlineStructDefinitionTest extends BaseStructTest
{
    /**
     * Name of fixture class to test
     *
     * @var string
     */
    protected $fixtureClass = InlineStructDefinition::class;

    public function testCreation()
    {
        $struct = $this->getTestStruct();
        static::assertEquals($struct->getExpectedContents(), $struct->toArray());
    }
}
