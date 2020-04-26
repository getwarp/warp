<?php

declare(strict_types=1);

namespace spaceonfire\DataSource\Exceptions;

use PHPUnit\Framework\TestCase;
use spaceonfire\ValueObject\UuidValue;

class ExceptionsTest extends TestCase
{
    public function testConstructDomainException(): void
    {
        $exception = new DomainException();
        self::assertEquals('Domain Exception', $exception->getMessage());
        self::assertEmpty($exception->getParameters());
    }

    public function testConstructRemoveException(): void
    {
        $exception = new RemoveException();
        self::assertEquals('Remove Exception', $exception->getMessage());
        self::assertEmpty($exception->getParameters());
    }

    public function testConstructNotFoundException(): void
    {
        $exception = new NotFoundException();
        self::assertEquals('Entity not found', $exception->getMessage());
        self::assertEmpty($exception->getParameters());
    }

    public function testConstructNotFoundExceptionWithPrimary(): void
    {
        $exception = new NotFoundException(null, [
            'primary' => 1,
        ]);
        self::assertEquals('Entity not found by primary "{primary}"', $exception->getMessage());
        self::assertEquals(['{primary}' => 1], $exception->getParameters());
    }

    public function testRender(): void
    {
        $exception = new NotFoundException(null, [
            'primary' => 1,
        ]);
        self::assertEquals('Entity not found by primary "1"', $exception->render());
    }

    public function testRenderWithPrepareMessage(): void
    {
        $exception = new NotFoundException(null, [
            'primary' => 1,
        ]);
        $res = $exception->render(static function () {
            return 'Запись не найдена по id: "{primary}"';
        });
        self::assertEquals('Запись не найдена по id: "1"', $res);
    }

    public function testRenderWithObject(): void
    {
        $uuid = UuidValue::random();
        $exception = new NotFoundException(null, [
            'primary' => $uuid,
        ]);
        self::assertEquals('Entity not found by primary "' . $uuid . '"', $exception->render());
    }
}
