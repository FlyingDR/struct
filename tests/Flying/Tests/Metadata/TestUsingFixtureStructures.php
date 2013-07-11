<?php

namespace Flying\Tests\Metadata;

use Flying\Struct\Configuration;
use Flying\Struct\ConfigurationManager;
use Flying\Tests\TestCaseUsingConfiguration;

/**
 * Basic class to allow using fixture structures into tests
 */
abstract class TestUsingFixtureStructures extends TestCaseUsingConfiguration
{

    protected $_annotationFixtures = array(
        'BasicStruct',
        'StructWithChild',
        'CustomPropertiesTest',
        'CustomAnnotationsTest',
        'ComplexStructure',
        'StructWithInvalidPropertyType',
        'StructWithInvalidStructureClass',
        'StructWithNonStructureClass',
        'InheritanceTestStructB',
    );

    public function setUp()
    {
        $configuration = new Configuration();
        $configuration->getStructNamespacesMap()->add('fixtures', 'Flying\Tests\Metadata\Fixtures\Structs');
        $configuration->getPropertyNamespacesMap()->add('fixtures', 'Flying\Tests\Metadata\Fixtures\Properties');
        $configuration->getAnnotationNamespacesMap()->add('fixtures', 'Flying\Tests\Metadata\Fixtures\Annotations');
        ConfigurationManager::setConfiguration($configuration);
    }

    public function getAnnotationFixtures()
    {
        $fixtures = array();
        foreach ($this->_annotationFixtures as $class) {
            $fixtures[] = array($class);
        }
        return $fixtures;
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
