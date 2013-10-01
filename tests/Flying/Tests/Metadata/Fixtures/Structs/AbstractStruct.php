<?php

namespace Flying\Tests\Metadata\Fixtures\Structs;

use Flying\Struct\ConfigurationManager;
use Flying\Tests\Metadata\Fixtures\Structs\MetadataTestcaseInterface;
use Flying\Tests\Metadata\Fixtures\Stubs\StructStub;

/**
 * @Struct\Boolean(name="from_abstract", default=true)
 */
abstract class AbstractStruct extends StructStub implements MetadataTestcaseInterface
{

}
