<?php

namespace Flying\Tests\Struct\Common;

use Flying\Struct\Common\ComplexPropertyInterface;
use Flying\Struct\ConfigurationManager;
use Flying\Struct\Struct;
use Flying\Tests\Struct\Fixtures\TestStruct;
use Flying\Tests\TestCaseUsingConfiguration;

abstract class BaseStructTest extends TestCaseUsingConfiguration
{
    /**
     * Namespace for fixtures structures
     *
     * @var string
     */
    protected $fixturesNs = 'Flying\Tests\Struct\Fixtures';
    /**
     * Name of fixture class to test
     *
     * @var string
     */
    protected $fixtureClass;

    public function setUp()
    {
        parent::setUp();
        ConfigurationManager::getConfiguration()->getStructNamespacesMap()->add($this->fixturesNs, 'fixtures');
    }

    public function testCountableInterface()
    {
        $struct = $this->getTestStruct();
        $expected = $struct->getExpectedContents();
        static::assertEquals(count($expected), count($struct));
    }

    /**
     * @param array|object $contents OPTIONAL Contents to initialize structure with
     * @param array|object $config   OPTIONAL Configuration for this structure
     * @return TestStruct
     */
    protected function getTestStruct($contents = null, $config = null)
    {
        $class = $this->getFixtureClass($this->fixtureClass);
        return new $class($contents, $config);
    }

    /**
     * Get FQCN for given fixture class
     *
     * @param string $class
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

    public function testIteratorInterface()
    {
        $struct = $this->getTestStruct();
        $contents = [];
        $expected = $struct->getExpectedContents();
        foreach ($struct as $key => $value) {
            if ($value instanceof ComplexPropertyInterface) {
                $contents[$key] = $value->toArray();
            } else {
                $contents[$key] = $value;
            }
        }
        static::assertEquals($expected, $contents);
    }

    public function testRecursiveIteratorInterface()
    {
        $struct = $this->getTestStruct();
        $expected = [];
        $temp = $struct->getExpectedContents();
        array_walk_recursive($temp, function ($v, $k) use (&$expected) {
            $expected[] = [$k, $v];
        });
        $actual = [];
        $iterator = new \RecursiveIteratorIterator($struct);
        foreach ($iterator as $key => $value) {
            $actual[] = [$key, $value];
        }
        static::assertEquals($expected, $actual);
    }

    public function testArrayAccessInterface()
    {
        $struct = $this->getTestStruct();
        $expected = $struct->getExpectedContents();
        foreach ($expected as $key => $value) {
            $sv = $struct[$key];
            if ($sv instanceof ComplexPropertyInterface) {
                $sv = $sv->toArray();
            }
            static::assertEquals($sv, $value);
        }
    }

    public function testConversionToArray()
    {
        $struct = $this->getTestStruct();
        static::assertEquals($struct->toArray(), $struct->getExpectedContents());
    }

    public function testSerialization()
    {
        $struct = $this->getTestStruct();
        $class = get_class($struct);
        $data = serialize($struct);
        /** @var $new Struct */
        $new = unserialize($data);
        static::assertInstanceOf($class, $new);
        static::assertNotEquals($struct, $new);
        static::assertEquals($struct->getExpectedContents(), $new->toArray());
    }
}
