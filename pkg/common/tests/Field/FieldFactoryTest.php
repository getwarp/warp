<?php

declare(strict_types=1);

namespace spaceonfire\Common\Field;

use PHPUnit\Framework\TestCase;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyReadInfo;
use Symfony\Component\PropertyInfo\PropertyReadInfoExtractorInterface;

class FieldFactoryTest extends TestCase
{
    public function testDefaultFieldFactory(): void
    {
        $factory = new DefaultFieldFactory();
        self::assertTrue($factory->enabled());

        $field = $factory->make('field');
        self::assertSame('field', (string)$field);
    }

    public function testYiiFieldFactory(): void
    {
        $factory = new YiiFieldFactory();
        self::assertTrue($factory->enabled());

        $field = $factory->make('field');
        self::assertSame('field', (string)$field);
    }

    public function testPropertyAccessFieldFactory(): void
    {
        $factory = new PropertyAccessFieldFactory();
        self::assertTrue($factory->enabled());

        $field = $factory->make('field');
        self::assertSame('field', (string)$field);
    }

    public function testPropertyAccessFieldFactoryWithCustomPropertyAccess(): void
    {
        $testReadInfoExtractor = new class implements PropertyReadInfoExtractorInterface {
            private ReflectionExtractor $reader;

            public bool $called = false;

            public function __construct()
            {
                $this->reader = new ReflectionExtractor();
            }

            public function getReadInfo(string $class, string $property, array $context = []): ?PropertyReadInfo
            {
                $this->called = true;
                return $this->reader->getReadInfo($class, $property, $context);
            }
        };
        $factory = new PropertyAccessFieldFactory();
        $factory->setPropertyAccessor(
            PropertyAccess::createPropertyAccessorBuilder()
                ->setReadInfoExtractor($testReadInfoExtractor)
                ->getPropertyAccessor()
        );

        $field = $factory->make('field');

        self::assertSame('foo', $field->extract((object)[
            'field' => 'foo',
        ]));
        self::assertTrue($testReadInfoExtractor->called);
    }

    public function testFieldFactoryAggregateEmpty(): void
    {
        $factory = new FieldFactoryAggregate();
        self::assertFalse($factory->enabled());
        self::assertSame([], \iterator_to_array($factory));
    }

    public function testFieldFactoryAggregateNotEnabled(): void
    {
        $disabled = new class implements FieldFactoryInterface {
            public function enabled(): bool
            {
                return false;
            }

            public function make(string $field): FieldInterface
            {
                throw new \RuntimeException();
            }
        };
        $factory = new FieldFactoryAggregate($disabled);
        self::assertFalse($factory->enabled());
        $this->expectException(\RuntimeException::class);
        $factory->make('field');
    }

    public function testFieldFactoryAggregate(): void
    {
        $disabled = new class implements FieldFactoryInterface {
            public function enabled(): bool
            {
                return false;
            }

            public function make(string $field): FieldInterface
            {
                throw new \RuntimeException();
            }
        };
        $default = new DefaultFieldFactory();
        $propertyAccess = new PropertyAccessFieldFactory();

        $factory = new FieldFactoryAggregate($disabled);
        self::assertFalse($factory->enabled());
        self::assertSame([$disabled], \iterator_to_array($factory));

        $factory = new FieldFactoryAggregate($disabled, $default);
        self::assertTrue($factory->enabled());
        self::assertSame([$disabled, $default], \iterator_to_array($factory));

        $field = $factory->make('field');
        self::assertInstanceOf(DefaultField::class, $field);

        $factory = new FieldFactoryAggregate($disabled, $propertyAccess, $default);
        self::assertTrue($factory->enabled());
        self::assertSame([$disabled, $propertyAccess, $default], \iterator_to_array($factory));

        $field = $factory->make('field');
        self::assertInstanceOf(PropertyAccessField::class, $field);
    }

    public function testFieldFactoryAggregateDefault(): void
    {
        $factory = FieldFactoryAggregate::default();
        $field = $factory->make('field');
        self::assertInstanceOf(PropertyAccessField::class, $field);
    }
}
