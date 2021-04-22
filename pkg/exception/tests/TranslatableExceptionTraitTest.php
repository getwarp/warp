<?php

declare(strict_types=1);

namespace spaceonfire\Exception;

use PHPUnit\Framework\TestCase;
use spaceonfire\Exception\Fixture\FixtureTranslatorFactory;
use Symfony\Contracts\Translation\TranslatableInterface;

class TranslatableExceptionTraitTest extends TestCase
{
    /**
     * @param string|\Stringable|MessageTemplate $message
     * @return \Throwable&TranslatableInterface
     */
    private function makeException($message = '', $code = 0, ?\Throwable $previous = null): \Throwable
    {
        return new class($message, $code, $previous) extends \Exception implements TranslatableInterface {
            use TranslatableExceptionTrait;

            public function __construct($message = '', $code = 0, ?\Throwable $previous = null)
            {
                $this->construct($message, $code, $previous);
            }
        };
    }

    public function testFromString(): void
    {
        $exception = $this->makeException('Boom.');

        $translator = FixtureTranslatorFactory::new()->make([
            'emoji' => [
                'Boom.' => '💥',
            ],
        ], 'emoji');

        self::assertSame('💥', $exception->trans($translator, 'emoji'));
    }

    public function testFromTemplate(): void
    {
        $exception = $this->makeException(MessageTemplate::new('Error: %reason%.', [
            '%reason%' => 'unknown',
        ]));

        $translator = FixtureTranslatorFactory::new()->make([
            'ru' => [
                'Error: %reason%.' => 'Ошибка: %reason%.',
            ],
        ]);

        self::assertSame('Ошибка: unknown.', $exception->trans($translator, 'ru'));
    }
}
