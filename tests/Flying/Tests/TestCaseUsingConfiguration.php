<?php

namespace Flying\Tests;

use Flying\Struct\Configuration;
use Flying\Struct\ConfigurationManager;

class TestCaseUsingConfiguration extends TestCase
{

    public function setUp()
    {
        // For tests that use configuration we must reset configuration for each test
        // to avoid side effects from inherited configuration contents
        ConfigurationManager::setConfiguration(new Configuration());
    }

}
