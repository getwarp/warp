<?php

declare(strict_types=1);

namespace spaceonfire\MonologBridge\Handler;

use Laminas\Hydrator\HydratorInterface;
use Laminas\Hydrator\ObjectPropertyHydrator;
use Monolog\Logger;
use spaceonfire\Bridge\LaminasHydrator\NamingStrategy\AliasNamingStrategy;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

final class MailerHandlerSettings extends AbstractHandlerSettings
{
    public ?MailerInterface $mailer = null;

    /**
     * @var null|Email|callable():Email
     */
    public $messageTemplate = null;

    /**
     * @var array<string,mixed>|null
     */
    public ?array $messageOptions = null;

    /**
     * @return Email|callable():Email
     */
    public function getMessageTemplate()
    {
        if (null !== $this->messageTemplate) {
            return $this->messageTemplate;
        }

        return function () {
            $email = new Email();

            foreach ($this->messageOptions ?? [] as $optionName => $optionValue) {
                \assert(\method_exists($email, $optionName));
                $email->{$optionName}($optionValue);
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

            $email->priority($priorityByLevelMap[$this->level]);

            return $email;
        };
    }

    protected static function hydrator(): HydratorInterface
    {
        $hydrator = new ObjectPropertyHydrator();

        $hydrator->setNamingStrategy(new AliasNamingStrategy([
            'messageOptions' => ['message_options', 'message-options'],
        ]));

        $hydrator->addStrategy('bubble', self::booleanStrategy());
        $hydrator->addStrategy('level', self::levelStrategy());

        return $hydrator;
    }
}
