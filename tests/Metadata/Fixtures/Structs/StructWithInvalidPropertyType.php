<?php

namespace Flying\Tests\Metadata\Fixtures\Structs;

use Flying\Struct\Annotation as Struct;
use Flying\Struct\Exception;
use Flying\Tests\Metadata\Fixtures\Stubs\StructStub;

/**
 * @Struct\Property(name="invalid", type="unknown")
 */
class StructWithInvalidPropertyType extends StructStub implements MetadataTestcaseInterface
{
    /**
     * Get array representation of expected results from parsing metadata of this class
     *
     * @return array
     */
    public function getExpectedMetadata()
    {
        return [];
    }

    /**
     * Get expected exception that should be raised when parsing metadata from this testcase
     *
     * @return string|array|null
     */
    public function getExpectedException()
    {
        return [Exception::class, 'Unable to resolve structure property class for type: unknown'];
    }
}
