<?php

declare(strict_types=1);

namespace Warp\Bridge\Cycle\Migrator\Exception;

use Symfony\Contracts\Translation\TranslatableInterface;
use Warp\Bridge\Cycle\Migrator\Input\InputOption;
use Warp\Exception\FriendlyExceptionTrait;
use Warp\Exception\MessageTemplate;
use Warp\Exception\TranslatableExceptionTrait;
use Yiisoft\FriendlyException\FriendlyExceptionInterface;

final class ConfirmationException extends \RuntimeException implements
    TranslatableInterface,
    FriendlyExceptionInterface
{
    use TranslatableExceptionTrait;
    use FriendlyExceptionTrait;

    /**
     * @param InputOption<mixed> $option
     * @noinspection MagicMethodsValidityInspection PhpMissingParentConstructorInspection
     */
    public function __construct(InputOption $option)
    {
        $this->solution = MessageTemplate::new('You need to confirm action or use %option% option.', [
            '%option%' => null === $option->getShortcut()
                ? \sprintf('`--%s`', $option->getName())
                : \sprintf('`--%s` (`-%s`)', $option->getName(), $option->getShortcut()),
        ]);

        $this->construct(
            <<<TEXT
            {$this->getName()}

            {$this->getSolution()}
            TEXT
        );
    }

    protected static function getDefaultName(): string
    {
        return 'Confirmation is required to run migrations';
    }
}
