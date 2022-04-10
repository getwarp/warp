<?php

declare(strict_types=1);

namespace Warp\DataSource;

use PHPUnit\Framework\TestCase;

class EntityNotFoundExceptionTest extends TestCase
{
    public function testConstructNotFoundException(): void
    {
        $exception = new EntityNotFoundException();
        self::assertSame('Entity not found', $exception->getName());
        self::assertSame('Entity not found', $exception->getMessage());
    }

    public function testConstructNotFoundExceptionWithPrimary(): void
    {
        $exception = EntityNotFoundException::byPrimary('post', 1);
        self::assertSame('Entity not found', $exception->getName());
        self::assertSame('Entity "post" not found by primary: 1.', $exception->getMessage());
    }

    public function testFactory(): void
    {
        $factory = new DefaultEntityNotFoundExceptionFactory();

        $exception = $factory->make('post', 1);
        self::assertSame('Entity not found', $exception->getName());
        self::assertSame('Entity "post" not found by primary: 1.', $exception->getMessage());
    }
}
