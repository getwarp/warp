<?php

declare(strict_types=1);

namespace spaceonfire\MonologBridge\Handler;

use Laminas\Hydrator\HydratorInterface;
use Monolog\Handler\HandlerInterface;
use Monolog\Logger;
use spaceonfire\LaminasHydratorBridge\NamingStrategy\AliasNamingStrategy;
use spaceonfire\LaminasHydratorBridge\StdClassHydrator;
use spaceonfire\LaminasHydratorBridge\Strategy\BooleanStrategy;
use Symfony\Bridge\Monolog\Handler\MailerHandler;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Webmozart\Assert\Assert;

final class MailerHandlerFactory extends AbstractHandlerFactory
{
    /**
     * @var MailerInterface|null
     */
    private $mailer;

    /**
     * MailerHandlerFactory constructor.
     * @param MailerInterface|null $mailer
     */
    public function __construct(?MailerInterface $mailer = null)
    {
        $this->mailer = $mailer;
    }

    /**
     * @inheritDoc
     */
    public function supportedTypes(): array
    {
        if (!class_exists(MailerHandler::class)) {
            return [];
        }

        return [
            'mailer',
            MailerHandler::class,
        ];
    }

    /**
     * @inheritDoc
     */
    public function make(array $parameters, CompositeHandlerFactory $factory): HandlerInterface
    {
        $parametersHydrated = $this->hydrateParameters($parameters);

        $parametersHydrated->level = isset($parametersHydrated->level)
            ? Logger::toMonologLevel($parametersHydrated->level)
            : Logger::DEBUG;

        $messageTemplate = static function () use ($parametersHydrated) {
            $email = new Email();

            foreach ($parametersHydrated->messageOptions as $optionName => $optionValue) {
                Assert::methodExists($email, $optionName);
                $email->$optionName($optionValue);
            }

            $priorityByLevelMap = [
                Logger::DEBUG => Email::PRIORITY_LOWEST,
                Logger::INFO => Email::PRIORITY_NORMAL,
                Logger::NOTICE => Email::PRIORITY_NORMAL,
                Logger::WARNING => Email::PRIORITY_HIGH,
                Logger::ERROR => Email::PRIORITY_HIGH,
                Logger::CRITICAL => Email::PRIORITY_HIGHEST,
                Logger::ALERT => Email::PRIORITY_HIGHEST,
                Logger::EMERGENCY => Email::PRIORITY_HIGHEST,
            ];

            $email->priority($priorityByLevelMap[$parametersHydrated->level]);

            return $email;
        };

        return new MailerHandler(
            $parametersHydrated->mailer ?? $this->mailer,
            $messageTemplate,
            $parametersHydrated->level,
            $parametersHydrated->bubble ?? true
        );
    }

    protected function getParametersHydrator(): ?HydratorInterface
    {
        $hydrator = new StdClassHydrator();

        $hydrator->setNamingStrategy(new AliasNamingStrategy([
            'messageOptions' => ['message_options', 'message-options'],
        ]));

        $boolHydratorStrategy = new BooleanStrategy(
            ['y', 'Y', 1],
            ['n', 'N', 0],
            false
        );

        $hydrator->addStrategy('bubble', $boolHydratorStrategy);

        return $hydrator;
    }
}
