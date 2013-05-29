<?php

namespace Flying\Tests\Struct;

use Flying\Struct\Configuration;
use Flying\Struct\ConfigurationManager;

abstract class BaseStructTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $configuration = new Configuration();
        $configuration->getStructNamespacesMap()->add('fixtures', 'Flying\Tests\Struct\Fixtures');
        ConfigurationManager::setConfiguration($configuration);
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
