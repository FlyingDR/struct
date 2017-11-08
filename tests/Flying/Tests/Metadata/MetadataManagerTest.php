<?php

namespace Flying\Tests\Metadata;

use Doctrine\Common\Cache\Cache;
use Flying\Struct\Configuration;
use Flying\Struct\ConfigurationManager;
use Flying\Struct\Metadata\AbstractMetadataParser;
use Flying\Struct\Metadata\AnnotationParser;
use Flying\Struct\Metadata\MetadataManager;
use Flying\Struct\Metadata\MetadataParserInterface;
use Flying\Struct\Metadata\StructMetadata;
use Flying\Tests\Metadata\Fixtures\Structs\BasicStruct;
use Flying\Tests\Metadata\Fixtures\Stubs\AbstractParserBasedParserStub;
use Flying\Tests\Metadata\Fixtures\Stubs\InterfaceBasedParserStub;
use Mockery;

class MetadataManagerTest extends TestUsingFixtureStructures
{
    public function testMetadataManagerConnectsItselfWithParserWhenGettingParser()
    {
        $parser = new AbstractParserBasedParserStub();
        $config = Mockery::mock('Flying\Struct\Configuration');
        $config->shouldReceive('getMetadataManager')->once()
            ->andReturn(new MetadataManager());
        $config->shouldReceive('getMetadataParser')->once()
            ->andReturn($parser);
        /** @var $config Configuration */
        ConfigurationManager::setConfiguration($config);
        $manager = $this->getTestManager();
        static::assertNotSame($parser->getMetadataManager(), $manager);
        /** @var AbstractMetadataParser $parserFromManager */
        $parserFromManager = $manager->getParser();
        static::assertSame($parserFromManager, $parser);
        static::assertSame($parserFromManager->getMetadataManager(), $manager);
    }

    public function testMetadataManagerConnectsItselfWithParserWhenSettingParser()
    {
        $manager = $this->getTestManager();
        $parser = new AbstractParserBasedParserStub();
        static::assertNotSame($manager, $parser->getMetadataManager());
        $manager->setParser($parser);
        static::assertSame($manager, $parser->getMetadataManager());
    }

    public function testMetadataManagerOnlyConnectsItselfWithParserWhenPossible()
    {
        $manager = $this->getTestManager();
        $parser = new InterfaceBasedParserStub();
        $manager->setParser($parser);
    }

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
        static::assertInstanceOf('Flying\Struct\Metadata\StructMetadata', $metadata);
        static::assertEquals($metadata->getClass(), $this->getFixtureClass('BasicStruct'));
        static::assertCount(4, $metadata->getProperties());
        static::assertArrayHasKey('first', $metadata->getProperties());
        static::assertArrayHasKey('second', $metadata->getProperties());
        static::assertArrayHasKey('third', $metadata->getProperties());
        static::assertArrayHasKey('fourth', $metadata->getProperties());
    }

    /**
     * @dataProvider dataProviderOfInvalidObjects
     */
    public function testGettingMetadataForInvalidObjects($object)
    {
        $this->setExpectedException(
            '\InvalidArgumentException',
            'Structure class must implement StructInterface interface'
        );
        $manager = $this->getTestManager();
        $manager->getMetadata($object);
    }

    public function dataProviderOfInvalidObjects()
    {
        return [
            ['\ArrayObject'],
            [new \ArrayObject()],
        ];
    }

    /**
     * @dataProvider dataProviderOfInvalidValues
     */
    public function testGettingMetadataForInvalidValues($value)
    {
        $this->setExpectedException(
            '\InvalidArgumentException',
            'Invalid structure information is given'
        );
        $manager = $this->getTestManager();
        $manager->getMetadata($value);
    }

    public function dataProviderOfInvalidValues()
    {
        return [
            [null],
            [true],
            [false],
            [[1, 2, 3]],
        ];
    }

    public function testManagerReceivesDependenciesFromConfigurationByDefault()
    {
        $config = Mockery::mock('Flying\Struct\Configuration');
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
        static::assertSame($parser, $manager->getParser());
        static::assertNotSame(ConfigurationManager::getConfiguration()->getMetadataParser(), $manager->getParser());
        $manager->setCache($cache);
        static::assertSame($cache, $manager->getCache());
        static::assertNotSame(ConfigurationManager::getConfiguration()->getCache(), $manager->getCache());
    }

    public function testManagerReturnsClonedMetadataObjects()
    {
        $manager = $this->getTestManager();
        $class = $this->getFixtureClass('BasicStruct');
        $m1 = $manager->getMetadata($class);
        $m2 = $manager->getMetadata($class);
        static::assertEquals($m1->toArray(), $m2->toArray());
        static::assertNotSame($m1, $m2);
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
        $cache = Mockery::mock('Doctrine\Common\Cache\Cache')
            ->shouldIgnoreMissing()
            ->shouldReceive('contains')->once()->andReturn(false)->getMock();
        /** @var $cache Cache */
        $manager->setCache($cache);

        $manager->getMetadata($class);
        // Second attempt to get same metadata should not cause calls to either metadata parser or cache
        $manager->getMetadata($class);
    }

    public function testManagerUsesResultsFromCacheIfAvailable()
    {
        $class = $this->getFixtureClass('BasicStruct');
        $manager = $this->getTestManager();
        $metadata = $manager->getMetadata($class);

        $manager = $this->getTestManager();
        $cache = Mockery::mock('Doctrine\Common\Cache\Cache')
            ->shouldReceive('contains')->once()->ordered()->andReturn(true)->getMock()
            ->shouldReceive('fetch')->once()->ordered()->andReturn($metadata)->getMock();
        /** @var $cache Cache */
        $manager->setCache($cache);

        $m = $manager->getMetadata($class);
        static::assertEquals($m->toArray(), $metadata->toArray());
        static::assertNotSame($m, $metadata);
    }

    public function testManagerStoresResultsInCache()
    {
        $class = $this->getFixtureClass('BasicStruct');
        $manager = $this->getTestManager();
        $metadata = $manager->getMetadata($class);

        $manager = $this->getTestManager();
        /** @var MetadataParserInterface $parser */
        $parser = Mockery::mock('Flying\Struct\Metadata\MetadataParserInterface')
            ->shouldReceive('getMetadata')->andReturn($metadata)->getMock();
        $manager->setParser($parser);
        $cache = Mockery::mock('Doctrine\Common\Cache\Cache')
            ->shouldReceive('contains')->once()->ordered()->andReturn(false)->getMock()
            ->shouldReceive('save')->once()->ordered()->with(Mockery::type('string'), get_class($metadata))->andReturnUndefined()->getMock();
        /** @var $cache Cache */
        $manager->setCache($cache);

        $m = $manager->getMetadata($class);
        static::assertEquals($m->toArray(), $metadata->toArray());
        static::assertNotSame($m, $metadata);
    }

    public function testManagerUsesOnlyValidCacheResults()
    {
        $manager = $this->getTestManager();
        $class = $this->getFixtureClass('BasicStruct');
        /** @var MetadataParserInterface $parser */
        $parser = Mockery::mock('Flying\Struct\Metadata\MetadataParserInterface')
            ->shouldReceive('getMetadata')->andReturn($this->getTestManager()->getMetadata($class))->getMock();
        $manager->setParser($parser);
        $cache = Mockery::mock('Doctrine\Common\Cache\Cache')
            ->shouldReceive('contains')->once()->ordered()->andReturn(true)->getMock()
            ->shouldReceive('fetch')->once()->ordered()->andReturn([])->getMock()
            ->shouldReceive('delete')->once()->ordered()->getMock()
            ->shouldReceive('save')->once()->ordered()->with(Mockery::type('string'), Mockery::any())->andReturnUndefined()->getMock();
        /** @var $cache Cache */
        $manager->setCache($cache);
        $manager->getMetadata($class);
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
        static::assertNull($metadata);
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
