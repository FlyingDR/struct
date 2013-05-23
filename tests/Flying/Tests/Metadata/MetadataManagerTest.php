<?php

namespace Flying\Tests\Metadata;

use Doctrine\Common\Cache\Cache;
use Flying\Struct\Configuration;
use Flying\Struct\ConfigurationManager;
use Flying\Struct\Metadata\AnnotationParser;
use Flying\Struct\Metadata\MetadataManager;
use Flying\Struct\Metadata\MetadataParserInterface;
use Flying\Struct\Metadata\StructMetadata;
use Flying\Tests\Metadata\Fixtures\Structs\BasicStruct;
use Mockery;

class MetadataManagerTest extends TestUsingFixtureStructures
{

    public function testMetadataRetrievingByClassName()
    {
        $manager = $this->getTestManager();
        $metadata = $manager->getMetadata($this->getFixtureClass('BasicStruct'));
        $this->validateMetadata($metadata);
    }

    public function testMetadataRetrievingByClassInstance()
    {
        $manager = $this->getTestManager();
        $metadata = $manager->getMetadata(new BasicStruct());
        $this->validateMetadata($metadata);
    }

    /**
     * Validate given structure metadata
     *
     * @param StructMetadata $metadata
     */
    protected function validateMetadata($metadata)
    {
        $this->assertInstanceOf('Flying\Struct\Metadata\StructMetadata', $metadata);
        $this->assertEquals($metadata->getClass(), $this->getFixtureClass('BasicStruct'));
        $this->assertEquals(4, sizeof($metadata->getProperties()));
        $this->assertArrayHasKey('first', $metadata->getProperties());
        $this->assertArrayHasKey('second', $metadata->getProperties());
        $this->assertArrayHasKey('third', $metadata->getProperties());
        $this->assertArrayHasKey('fourth', $metadata->getProperties());
    }

    /**
     * @dataProvider dataProviderOfInvalidObjects
     */
    public function testGettingMetadataForInvalidObjects($object)
    {
        $this->setExpectedException('\InvalidArgumentException', 'Structure class must implement StructInterface interface');
        $manager = $this->getTestManager();
        $manager->getMetadata($object);
    }

    public function dataProviderOfInvalidObjects()
    {
        return array(
            array('\ArrayObject'),
            array(new \ArrayObject()),
        );
    }

    /**
     * @dataProvider dataProviderOfInvalidValues
     */
    public function testGettingMetadataForInvalidValues($value)
    {
        $this->setExpectedException('\InvalidArgumentException', 'Invalid structure information is given');
        $manager = $this->getTestManager();
        $manager->getMetadata($value);
    }

    public function dataProviderOfInvalidValues()
    {
        return array(
            array(null),
            array(true),
            array(false),
            array(array(1, 2, 3)),
        );
    }

    public function testManagerReceivesDependenciesFromConfigurationByDefault()
    {
        $config = Mockery::mock('\Flying\Struct\Configuration');
        $config->shouldReceive('getMetadataParser')->once()
            ->andReturn(new AnnotationParser());
        $config->shouldReceive('getCache')->once()
            ->andReturn(ConfigurationManager::getConfiguration()->getCache());
        /** @var $config Configuration */
        ConfigurationManager::setConfiguration($config);
        $manager = $this->getTestManager();
        $manager->getParser();
        $manager->getCache();
    }

    public function testManagerUsesGivenDependencies()
    {
        $parser = clone ConfigurationManager::getConfiguration()->getMetadataParser();
        $cache = clone ConfigurationManager::getConfiguration()->getCache();
        $manager = $this->getTestManager();
        $manager->setParser($parser);
        $this->assertTrue($manager->getParser() === $parser);
        $this->assertFalse($manager->getParser() === ConfigurationManager::getConfiguration()->getMetadataParser());
        $manager->setCache($cache);
        $this->assertTrue($manager->getCache() === $cache);
        $this->assertFalse($manager->getCache() === ConfigurationManager::getConfiguration()->getCache());
    }

    public function testManagerReturnsClonedMetadataObjects()
    {
        $manager = $this->getTestManager();
        $class = $this->getFixtureClass('BasicStruct');
        $m1 = $manager->getMetadata($class);
        $m2 = $manager->getMetadata($class);
        $this->assertEquals($m1->toArray(), $m2->toArray());
        $this->assertFalse($m1 === $m2);
    }

    public function testManagerCachesResultsLocally()
    {
        $class = $this->getFixtureClass('BasicStruct');
        $manager = $this->getTestManager();
        $metadata = $manager->getMetadata($class);

        $manager = $this->getTestManager();
        $parser = Mockery::mock('Flying\Struct\Metadata\MetadataParserInterface');
        $parser->shouldReceive('getMetadata')->once()
            ->andReturn($metadata);
        /** @var $parser MetadataParserInterface */
        $manager->setParser($parser);
        $cache = Mockery::mock('Doctrine\Common\Cache\Cache');
        $cache->shouldReceive('contains')->once()
            ->andReturn(false);
        /** @var $cache Cache */
        $manager->setCache($cache);

        $manager->getMetadata($class);
        // Second attempt to get same metadata should not cause calls to either metadata parser or cache
        $manager->getMetadata($class);
    }

    public function testManagerStoresResultsInCache()
    {
        $class = $this->getFixtureClass('BasicStruct');
        $manager = $this->getTestManager();
        $metadata = $manager->getMetadata($class);

        $manager = $this->getTestManager();
        $cache = Mockery::mock('Doctrine\Common\Cache\Cache');
        $cache->shouldReceive('contains')->once()->ordered()
            ->andReturn(true);
        $cache->shouldReceive('fetch')->once()->ordered()
            ->andReturn($metadata);
        /** @var $cache Cache */
        $manager->setCache($cache);

        $m = $manager->getMetadata($class);
        $this->assertEquals($m->toArray(), $metadata->toArray());
        $this->assertFalse($m === $metadata);
    }

    public function testManagerUsesOnlyValidCacheResults()
    {
        $manager = $this->getTestManager();
        $cache = Mockery::mock('Doctrine\Common\Cache\Cache');
        $cache->shouldReceive('contains')->once()->ordered()
            ->andReturn(true);
        $cache->shouldReceive('fetch')->once()->ordered()
            ->andReturn(array());
        $cache->shouldReceive('delete')->once()->ordered();
        /** @var $cache Cache */
        $manager->setCache($cache);
        $manager->getMetadata($this->getFixtureClass('BasicStruct'));
    }

    public function testManagerReturnsNullIfNoResultsAvailable()
    {
        $manager = $this->getTestManager();
        $parser = Mockery::mock('Flying\Struct\Metadata\MetadataParserInterface');
        $parser->shouldReceive('getMetadata')->once()
            ->andReturn(null);
        /** @var $parser MetadataParserInterface */
        $manager->setParser($parser);
        $cache = Mockery::mock('Doctrine\Common\Cache\Cache');
        $cache->shouldReceive('contains')->once()
            ->andReturn(false);
        /** @var $cache Cache */
        $manager->setCache($cache);

        $metadata = $manager->getMetadata($this->getFixtureClass('BasicStruct'));
        $this->assertNull($metadata);
    }

    /**
     * Get tested object instance
     *
     * @return MetadataManager
     */
    protected function getTestManager()
    {
        return new MetadataManager();
    }

}
