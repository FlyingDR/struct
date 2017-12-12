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
    private static $annotationFixtures = [
        'BasicStruct',
        'StructWithChild',
        'CustomPropertiesTest',
        'CustomAnnotationsTest',
        'ComplexStructure',
        'StructWithInvalidPropertyType',
        'StructWithInvalidStructureClass',
        'StructWithNonStructureClass',
        'InheritanceTestStructB',
        'InlineStructDefinition',
        'StructInheritedFromAbstract',
        'StructWithMetadataModifications',
    ];

    public function setUp()
    {
        $configuration = new Configuration();
        $configuration->getStructNamespacesMap()->add('Flying\Tests\Metadata\Fixtures\Structs', 'fixtures');
        $configuration->getPropertyNamespacesMap()->add('Flying\Tests\Metadata\Fixtures\Properties', 'fixtures');
        ConfigurationManager::setConfiguration($configuration);
    }

    public function getAnnotationFixtures()
    {
        $fixtures = [];
        foreach (self::$annotationFixtures as $class) {
            $fixtures[] = [$class];
        }
        return $fixtures;
    }

    /**
     * Get FQCN for given fixture class
     *
     * @param string $class
     *
     * @return string
     */
    protected function getFixtureClass($class)
    {
        if (class_exists($class)) {
            return $class;
        }
        $class = ucfirst(trim($class, '\\'));
        $namespaces = ConfigurationManager::getConfiguration()->getStructNamespacesMap()->getAll();
        foreach ($namespaces as $ns) {
            $nsClass = $ns . '\\' . $class;
            if (class_exists($nsClass)) {
                return $nsClass;
            }
        }
        static::fail('Unable to find fixture class: ' . $class);
        return null;
    }
}
