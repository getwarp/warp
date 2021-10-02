<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Monolog\Handler;

use Monolog\Logger;
use Monolog\Test\TestCase;
use spaceonfire\Bridge\Monolog\Fixture\FixtureFormatter;
use Symfony\Bridge\Monolog\Handler\MailerHandler;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\RawMessage;

class MailerHandlerFactoryTest extends TestCase
{
    private function makeTransport()
    {
        return new class implements TransportInterface {
            /**
             * @var Email[]
             */
            public array $messages = [];

            public function send(RawMessage $message, Envelope $envelope = null): ?SentMessage
            {
                $this->messages[] = $message;
                return null;
            }

            public function getLastMessage(): ?Email
            {
                return \end($this->messages) ?: null;
            }

            public function __toString(): string
            {
                return '';
            }
        };
    }

    public function testDefault(): void
    {
        $transport = $this->makeTransport();
        $factory = new MailerHandlerFactory(new Mailer($transport));

        self::assertSame(['mailer', MailerHandler::class], $factory->supportedTypes());

        $handler = $factory->make([
            'messageOptions' => [
                'subject' => 'test monolog',
            ],
            'formatter' => FixtureFormatter::class,
        ]);

        $handler->handle($this->getRecord(Logger::WARNING, 'warning'));

        self::assertCount(1, $transport->messages);
        $message = $transport->getLastMessage();
        self::assertSame('test monolog', $message->getSubject());
        self::assertSame('warning', $message->getTextBody());
    }

    public function testMessageTemplate(): void
    {
        $transport = $this->makeTransport();
        $factory = new MailerHandlerFactory(new Mailer($transport));

        self::assertSame(['mailer', MailerHandler::class], $factory->supportedTypes());

        $template = new Email();
        $template->subject('monolog');
        $address = Address::create('John Doe <foo@bar.baz>');
        $template->sender($address);

        $handler = $factory->make([
            'messageTemplate' => $template,
        ]);

        $handler->handle($this->getRecord(Logger::WARNING, 'warning'));

        self::assertCount(1, $transport->messages);
        $message = $transport->getLastMessage();
        self::assertSame('monolog', $message->getSubject());
        self::assertSame($address, $message->getSender());
    }
}
