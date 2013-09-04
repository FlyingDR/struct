<?php

namespace Flying\Tests\Struct\Common;

use Flying\Struct\Configuration;
use Flying\Struct\ConfigurationManager;
use Flying\Tests\TestCaseUsingConfiguration;

abstract class BaseStructTest extends TestCaseUsingConfiguration
{
    /**
     * Namespace for fixtures structures
     * @var string
     */
    protected $_fixturesNs = 'Flying\Tests\Struct\Fixtures';

    public function setUp()
    {
        parent::setUp();
        ConfigurationManager::getConfiguration()->getStructNamespacesMap()->add($this->_fixturesNs, 'fixtures');
    }

    /**
     * Get FQCN for given fixture class
     *
     * @param string $class
     * @return string
     */
    protected function getFixtureClass($class)
    {
        if (class_exists($class, true)) {
            return $class;
        }
        $class = trim(ucfirst($class), '\\');
        $namespaces = ConfigurationManager::getConfiguration()->getStructNamespacesMap()->getAll();
        foreach ($namespaces as $ns) {
            $nsClass = $ns . '\\' . $class;
            if (class_exists($nsClass, true)) {
                return $nsClass;
            }
        }
        $this->fail('Unable to find fixture class: ' . $class);
        return null;
    }

}
