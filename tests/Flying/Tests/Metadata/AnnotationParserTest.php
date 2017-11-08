<?php

namespace Flying\Tests\Metadata;

use Doctrine\Common\Annotations\Reader;
use Flying\Struct\ConfigurationManager;
use Flying\Struct\Exception;
use Flying\Struct\Metadata\AnnotationParser;
use Flying\Struct\Metadata\MetadataManagerInterface;
use Flying\Struct\Metadata\StructMetadata;
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
            $this->expectException($exception);
            $this->expectExceptionMessage($message);
            $parser->getMetadata($class);
        } else {
            $metadata = $parser->getMetadata($class);
            static::assertInstanceOf(StructMetadata::class, $metadata);
            $expected = $fixture->getExpectedMetadata();
            $actual = $metadata->toArray();
            array_walk_recursive($actual, function (&$v, $k) {
                if ($k === 'hash') {
                    $v = 'test';
                }
            });
            static::assertEquals($expected, $actual);
        }
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

    public function testMetadataReceivingFailure()
    {
        $manager = Mockery::mock(MetadataManagerInterface::class);
        $manager->shouldReceive('getMetadata')->andReturnNull();
        /** @var $manager MetadataManagerInterface */
        ConfigurationManager::getConfiguration()->setMetadataManager($manager);
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Failed to get structure metadata for class: ' . $this->getFixtureClass('BasicStruct'));
        $parser = $this->getTestParser();
        $parser->getMetadata($this->getFixtureClass('StructWithChild'));
    }

    public function testParserUsesGivenReader()
    {
        $parser = $this->getTestParser();
        $reader = Mockery::mock(Reader::class);
        $reader->shouldReceive('getClassAnnotations')->once()
            ->andReturn([]);
        /** @var $reader Reader */
        $parser->setReader($reader);
        $metadata = $parser->getMetadata($this->getFixtureClass('BasicStruct'));
        static::assertInstanceOf(StructMetadata::class, $metadata);
        static::assertEmpty($metadata->getProperties());
    }
}
