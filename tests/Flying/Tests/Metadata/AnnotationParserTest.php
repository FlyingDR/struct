<?php

namespace Flying\Tests\Metadata;

use Doctrine\Common\Annotations\Reader;
use Flying\Struct\Configuration;
use Flying\Struct\ConfigurationManager;
use Flying\Struct\Metadata\AnnotationParser;
use Flying\Struct\Metadata\MetadataManagerInterface;
use Flying\Tests\Metadata\Fixtures\Structs\MetadataTestcaseInterface;
use Mockery;

class AnnotationParserTest extends TestUsingFixtureStructures
{

    /**
     * @param string $class
     * @dataProvider getAnnotationFixtures
     */
    public function testParsingAnnotationFixture($class)
    {
        $parser = $this->getTestParser();
        $class = $this->getFixtureClass($class);
        /** @var $fixture MetadataTestcaseInterface */
        $fixture = new $class();
        $expectedException = $fixture->getExpectedException();
        if ($expectedException) {
            $exception = null;
            $message = '';
            if (is_array($expectedException)) {
                $exception = array_shift($expectedException);
                $message = array_shift($expectedException);
            } else {
                $exception = $expectedException;
            }
            $this->setExpectedException($exception, $message);
            $parser->getMetadata($class);
        } else {
            $metadata = $parser->getMetadata($class);
            $this->assertInstanceOf('Flying\Struct\Metadata\StructMetadata', $metadata);
            $expected = $fixture->getExpectedMetadata();
            $actual = $metadata->toArray();
            array_walk_recursive($actual, function (&$v, $k) {
                if ($k == 'hash') {
                    $v = 'test';
                };
            });
            $this->assertEquals($expected, $actual);
        }
    }

    public function testMetadataReceivingFailure()
    {
        $manager = Mockery::mock('Flying\Struct\Metadata\MetadataManagerInterface');
        $manager->shouldReceive('getMetadata')->once()
            ->andReturnNull();
        /** @var $manager MetadataManagerInterface */
        ConfigurationManager::getConfiguration()->setMetadataManager($manager);
        $this->setExpectedException('Flying\Struct\Exception', 'Failed to get structure metadata for class: ' . $this->getFixtureClass('BasicStruct'));
        $parser = $this->getTestParser();
        $parser->getMetadata($this->getFixtureClass('StructWithChild'));
    }

    public function testParserUsesGivenReader()
    {
        $parser = $this->getTestParser();
        $reader = Mockery::mock('Doctrine\Common\Annotations\Reader');
        $reader->shouldReceive('getClassAnnotations')->twice()
            ->andReturn(array());
        /** @var $reader Reader */
        $parser->setReader($reader);
        $metadata = $parser->getMetadata($this->getFixtureClass('BasicStruct'));
        $this->assertInstanceOf('Flying\Struct\Metadata\StructMetadata', $metadata);
        $this->assertEmpty($metadata->getProperties());
    }

    /**
     * Get new instance of parser class being tested
     *
     * @return AnnotationParser
     */
    protected function getTestParser()
    {
        return new AnnotationParser();
    }

}
