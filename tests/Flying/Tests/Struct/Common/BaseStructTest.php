<?php

namespace Flying\Tests\Struct\Common;

use Flying\Struct\Common\ComplexPropertyInterface;
use Flying\Struct\Configuration;
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
    protected $fixtureClass = null;

    public function setUp()
    {
        parent::setUp();
        ConfigurationManager::getConfiguration()->getStructNamespacesMap()->add($this->fixturesNs, 'fixtures');
    }

    public function testCountableInterface()
    {
        $struct = $this->getTestStruct();
        $expected = $struct->getExpectedContents();
        $this->assertEquals(sizeof($expected), sizeof($struct));
    }

    public function testIteratorInterface()
    {
        $struct = $this->getTestStruct();
        $contents = array();
        $expected = $struct->getExpectedContents();
        foreach ($struct as $key => $value) {
            if ($value instanceof ComplexPropertyInterface) {
                $contents[$key] = $value->toArray();
            } else {
                $contents[$key] = $value;
            }
        }
        $this->assertEquals($expected, $contents);
    }

    public function testRecursiveIteratorInterface()
    {
        $struct = $this->getTestStruct();
        $expected = array();
        $temp = $struct->getExpectedContents();
        array_walk_recursive($temp, function ($v, $k) use (&$expected) {
            $expected[] = array($k, $v);
        });
        $actual = array();
        $iterator = new \RecursiveIteratorIterator($struct);
        foreach ($iterator as $key => $value) {
            $actual[] = array($key, $value);
        }
        $this->assertEquals($expected, $actual);
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
            $this->assertEquals($sv, $value);
        }
    }

    public function testConversionToArray()
    {
        $struct = $this->getTestStruct();
        $this->assertEquals($struct->toArray(), $struct->getExpectedContents());
    }

    public function testSerialization()
    {
        $struct = $this->getTestStruct();
        $class = get_class($struct);
        $data = serialize($struct);
        /** @var $new Struct */
        $new = unserialize($data);
        $this->assertInstanceOf($class, $new);
        $this->assertNotEquals($struct, $new);
        $this->assertEquals($struct->getExpectedContents(), $new->toArray());
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

    /**
     * @param array|object $contents OPTIONAL Contents to initialize structure with
     * @param array|object $config   OPTIONAL Configuration for this structure
     * @return TestStruct
     */
    protected function getTestStruct($contents = null, $config = null)
    {
        $class = $this->getFixtureClass($this->fixtureClass);
        $struct = new $class($contents, $config);
        return $struct;
    }
}
