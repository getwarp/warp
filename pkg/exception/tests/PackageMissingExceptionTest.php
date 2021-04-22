<?php

declare(strict_types=1);

namespace spaceonfire\Exception;

use PHPUnit\Framework\TestCase;

class PackageMissingExceptionTest extends TestCase
{
    public function testPackage(): void
    {
        $exception = PackageMissingException::new('spaceonfire/wasabi');

        self::assertSame('Package missing', $exception->getName());
        self::assertSame('Package "spaceonfire/wasabi" is not installed.', $exception->getMessage());
        self::assertSame('Run "composer require spaceonfire/wasabi" in command line.', $exception->getSolution());
    }

    public function testPackageScope(): void
    {
        $exception = PackageMissingException::new('spaceonfire/wasabi', null, 'Sushi');

        self::assertSame('Package missing', $exception->getName());
        self::assertSame('Sushi requires package "spaceonfire/wasabi", which seems is not installed.', $exception->getMessage());
        self::assertSame('Run "composer require spaceonfire/wasabi" in command line.', $exception->getSolution());
    }

    public function testPackageVersionScope(): void
    {
        $exception = PackageMissingException::new('spaceonfire/wasabi', '^1.0', 'Sushi');

        self::assertSame('Package missing', $exception->getName());
        self::assertSame('Sushi requires package "spaceonfire/wasabi", which seems is not installed.', $exception->getMessage());
        self::assertSame('Run "composer require spaceonfire/wasabi:^1.0" in command line.', $exception->getSolution());
    }
}
