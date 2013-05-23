<?php

namespace Flying\Tests\Metadata\Fixtures\Structs;

/**
 * Interface for classes that provides test cases
 * for annotations metadata parser
 */
interface MetadataTestcaseInterface
{
    /**
     * Get array representation of expected results from parsing metadata of this class
     *
     * @return array
     */
    public function getExpectedMetadata();

    /**
     * Get expected exception that should be raised when parsing metadata from this testcase
     *
     * @return string|array|null
     */
    public function getExpectedException();

}
