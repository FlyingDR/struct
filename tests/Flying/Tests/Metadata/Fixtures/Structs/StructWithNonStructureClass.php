<?php

namespace Flying\Tests\Metadata\Fixtures\Structs;

use Flying\Struct\ConfigurationManager;
use Flying\Tests\Metadata\Fixtures\Structs\MetadataTestcaseInterface;
use Flying\Tests\Metadata\Fixtures\Stubs\StructStub;

/**
 * @Struct\Struct(name="invalid", class="\ArrayObject")
 */
class StructWithNonStructureClass extends StructStub implements MetadataTestcaseInterface
{

    /**
     * Get array representation of expected results from parsing metadata of this class
     *
     * @return array
     */
    public function getExpectedMetadata()
    {
        return array();
    }

    /**
     * Get expected exception that should be raised when parsing metadata from this testcase
     *
     * @return string|array|null
     */
    public function getExpectedException()
    {
        return array('Flying\Struct\Exception', 'Unable to resolve structure class: \ArrayObject');
    }

}