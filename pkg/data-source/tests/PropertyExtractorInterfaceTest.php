<?php

declare(strict_types=1);

namespace Warp\DataSource;

use Laminas\Hydrator\HydratorInterface;
use Laminas\Hydrator\NamingStrategy\MapNamingStrategy;
use Laminas\Hydrator\ObjectPropertyHydrator;
use Laminas\Hydrator\Strategy\ClosureStrategy;
use PHPUnit\Framework\TestCase;

class PropertyExtractorInterfaceTest extends TestCase
{
    public function testIdentical(): void
    {
        $extractor = new IdenticalPropertyExtractor();

        self::assertSame('foo', $extractor->extractName('foo'));
        self::assertSame('bar', $extractor->extractValue('foo', 'bar'));
    }

    public function testHydrator(): void
    {
        $hydrator = new ObjectPropertyHydrator();
        $hydrator->setNamingStrategy(MapNamingStrategy::createFromHydrationMap([
            'foo' => 'foofoo',
            'bar' => 'foobar',
            'baz' => 'foobaz',
        ]));
        $hydrator->addStrategy('foofoo', new ClosureStrategy(static fn () => 'foobarbaz'));

        $extractor = new LaminasPropertyExtractor($hydrator);

        self::assertSame('foo', $extractor->extractName('foofoo'));
        self::assertSame('bar', $extractor->extractName('foobar'));
        self::assertSame('foobarbaz', $extractor->extractValue('foofoo', 'bar'));
    }

    public function testHydratorWithoutStrategies(): void
    {
        $hydrator = new class implements HydratorInterface {
            public function extract(object $object): array
            {
                throw new \RuntimeException();
            }

            public function hydrate(array $data, object $object)
            {
                throw new \RuntimeException();
            }
        };

        $extractor = new LaminasPropertyExtractor($hydrator);

        self::assertSame('foo', $extractor->extractName('foo'));
        self::assertSame('bar', $extractor->extractValue('foo', 'bar'));
    }
}
