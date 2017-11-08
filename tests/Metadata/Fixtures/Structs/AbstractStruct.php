<?php

namespace Flying\Tests\Metadata\Fixtures\Structs;

use Flying\Tests\Metadata\Fixtures\Stubs\StructStub;

/**
 * @Struct\Boolean(name="from_abstract", default=true)
 */
abstract class AbstractStruct extends StructStub implements MetadataTestcaseInterface
{

}
