<?php

declare(strict_types=1);

namespace Warp\Exception;

use PHPUnit\Framework\TestCase;
use Warp\Exception\Fixture\FixtureFriendlyException;

class FriendlyExceptionTraitTest extends TestCase
{
    public function testDefaults(): void
    {
        $exception = FixtureFriendlyException::new('Error.');

        self::assertSame('Fixture friendly exception', $exception->getName());
        self::assertNull($exception->getSolution());
    }

    public function testCustomNameAndSolution(): void
    {
        $exception = FixtureFriendlyException::new(
            'Error.',
            'Custom Friendly Exception',
            'Lorem ipsum...',
        );

        self::assertSame('Custom Friendly Exception', $exception->getName());
        self::assertSame('Lorem ipsum...', $exception->getSolution());
    }

    public function testCustomNameAndSolutionStringable(): void
    {
        $exception = FixtureFriendlyException::new(
            'Error.',
            MessageTemplate::new('Custom Friendly Exception'),
            MessageTemplate::new('Lorem ipsum...'),
        );

        self::assertSame('Custom Friendly Exception', $exception->getName());
        self::assertSame('Lorem ipsum...', $exception->getSolution());
    }
}
