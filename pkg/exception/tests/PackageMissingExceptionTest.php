<?php

declare(strict_types=1);

namespace Warp\Exception;

use PHPUnit\Framework\TestCase;

class PackageMissingExceptionTest extends TestCase
{
    public function testPackage(): void
    {
        $exception = PackageMissingException::new('getwarp/wasabi');

        self::assertSame('Package missing', $exception->getName());
        self::assertSame('Package "getwarp/wasabi" is not installed.', $exception->getMessage());
        self::assertSame('Run "composer require getwarp/wasabi" in command line.', $exception->getSolution());
    }

    public function testPackageScope(): void
    {
        $exception = PackageMissingException::new('getwarp/wasabi', null, 'Sushi');

        self::assertSame('Package missing', $exception->getName());
        self::assertSame('Sushi requires package "getwarp/wasabi", which seems is not installed.', $exception->getMessage());
        self::assertSame('Run "composer require getwarp/wasabi" in command line.', $exception->getSolution());
    }

    public function testPackageVersionScope(): void
    {
        $exception = PackageMissingException::new('getwarp/wasabi', '^1.0', 'Sushi');

        self::assertSame('Package missing', $exception->getName());
        self::assertSame('Sushi requires package "getwarp/wasabi", which seems is not installed.', $exception->getMessage());
        self::assertSame('Run "composer require getwarp/wasabi:^1.0" in command line.', $exception->getSolution());
    }
}
