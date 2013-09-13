<?php

namespace Flying\Tests\Struct\Common;

use Mockery;

/**
 * @method \Flying\Tests\Struct\Fixtures\InlineStructDefinition getTestStruct($contents = null, $config = null)
 */
abstract class InlineStructDefinitionTest extends BaseStructTest
{
    /**
     * Name of fixture class to test
     * @var string
     */
    protected $fixtureClass = 'Flying\Tests\Struct\Fixtures\InlineStructDefinition';

    public function testCreation()
    {
        $struct = $this->getTestStruct();
        $this->assertEquals($struct->getExpectedContents(), $struct->toArray());
    }
}
