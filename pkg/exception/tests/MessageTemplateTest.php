<?php

declare(strict_types=1);

namespace spaceonfire\Exception;

use PHPUnit\Framework\TestCase;
use spaceonfire\Exception\Fixture\FixtureTranslatorFactory;

class MessageTemplateTest extends TestCase
{
    public function testSimpleMessage(): void
    {
        $message = MessageTemplate::new('Hello, world!');

        self::assertSame('Hello, world!', (string)$message);

        $translator = FixtureTranslatorFactory::new()->make([
            'en' => [
                'Hello, world!' => 'Hello, world!',
            ],
            'ru' => [
                'Hello, world!' => 'Привет, мир!',
            ],
        ]);

        self::assertSame('Привет, мир!', $message->trans($translator, 'ru'));
    }

    public function testMessageWithParameters(): void
    {
        $message = MessageTemplate::new('Hello, %name%!', ['%name%' => 'John Doe']);

        self::assertSame('Hello, John Doe!', (string)$message);

        $translator = FixtureTranslatorFactory::new()->make([
            'ru' => [
                'Hello, %name%!' => 'Привет, %name%!',
            ],
        ], 'ru');

        self::assertSame('Привет, John Doe!', $message->trans($translator));
    }

    public function testMessageWithDomain(): void
    {
        $message = MessageTemplate::new('world');
        $message->setDomain('no-vowels');

        self::assertSame('world', (string)$message);

        $translator = FixtureTranslatorFactory::new()->make([
            'en' => [
                'world' => 'wrld',
            ],
        ], 'en', 'no-vowels');

        self::assertSame('wrld', $message->trans($translator));
    }

    public function testWrap(): void
    {
        $message = MessageTemplate::wrap('Hello, world!');
        self::assertSame('Hello, world!', (string)$message);
        self::assertSame($message, MessageTemplate::wrap($message));
    }
}
