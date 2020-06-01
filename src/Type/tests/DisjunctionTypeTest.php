<?php

declare(strict_types=1);

namespace spaceonfire\Type;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class DisjunctionTypeTest extends TestCase
{
    public function testSupports(): void
    {
        self::assertTrue(DisjunctionType::supports('int|null'));
        self::assertFalse(DisjunctionType::supports('int'));
    }

    public function testCreate(): void
    {
        DisjunctionType::create('int|null');
        self::assertTrue(true);
    }

    public function testCreateFail(): void
    {
        $this->expectException(InvalidArgumentException::class);
        DisjunctionType::create('int');
    }

    public function testCheck(): void
    {
        $type = DisjunctionType::create('integer|null');
        self::assertTrue($type->check(1));
        self::assertTrue($type->check(null));
        self::assertFalse($type->check('1'));
    }

    public function testStringify(): void
    {
        $type = DisjunctionType::create('integer|null');
        self::assertEquals('int|null', (string)$type);
    }
}
