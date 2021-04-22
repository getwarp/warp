<?php

declare(strict_types=1);

namespace spaceonfire\Type\Factory;

use PHPUnit\Framework\TestCase;
use spaceonfire\Type\Fixtures\InvalidAggregatedTypeFactory;

class AbstractAggregatedTypeFactoryTest extends TestCase
{
    public function testAggregatedTypeFactoryWithInvalidTypeClass(): void
    {
        $this->expectException(\RuntimeException::class);
        new InvalidAggregatedTypeFactory();
    }

    public function testAggregatedTypeFactoryWithoutTypeClass(): void
    {
        $this->expectException(\RuntimeException::class);
        new class extends AbstractAggregatedTypeFactory {
        };
    }
}
