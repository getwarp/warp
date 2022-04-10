<?php

declare(strict_types=1);

namespace Warp\Type;

use PHPUnit\Framework\TestCase;
use Warp\Type\Fixtures\InvalidAggregatedType;

class AbstractAggregatedTypeTest extends TestCase
{
    public function testInvalidAggregatedTypeClass(): void
    {
        $this->expectException(\LogicException::class);
        InvalidAggregatedType::new(BuiltinType::string(), BuiltinType::int());
    }
}
