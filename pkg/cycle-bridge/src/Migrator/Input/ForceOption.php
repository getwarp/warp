<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Migrator\Input;

use Cycle\Migrations\Config\MigrationConfig;
use Psr\Container\ContainerInterface;
use spaceonfire\Bridge\Cycle\Migrator\Exception\ConfirmationException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Style\StyleInterface;

/**
 * @extends InputOption<bool>
 */
final class ForceOption extends InputOption
{
    private bool $confirmDefault;

    private string $confirmQuestion;

    /**
     * @param string|string[] $shortcut
     */
    public function __construct(
        string $name = 'force',
        $shortcut = 'f',
        string $description = 'Force run the operation',
        bool $default = false,
        string $confirmQuestion = 'Would you like to continue?'
    ) {
        parent::__construct($name, $shortcut, self::VALUE_NONE, $description);

        $this->confirmDefault = $default;
        $this->confirmQuestion = $confirmQuestion;
    }

    public function getDefault(): bool
    {
        return $this->confirmDefault;
    }

    /**
     * @param bool $default
     */
    public function setDefault($default = null): void
    {
        $this->confirmDefault = (bool)$default;
    }

    public function getConfirmQuestion(): string
    {
        return $this->confirmQuestion;
    }

    public function setConfirmQuestion(string $confirmQuestion): void
    {
        $this->confirmQuestion = $confirmQuestion;
    }

    public function confirm(InputInterface $input, StyleInterface $style): void
    {
        $value = $this->getValueFrom($input);

        if (true === $value) {
            return;
        }

        $default = $this->getDefault();
        $options = \sprintf(' [%s/%s]', $default ? 'Y' : 'y', $default ? 'n' : 'N');

        if ($style->confirm($this->confirmQuestion . $options, $default)) {
            return;
        }

        throw new ConfirmationException($this);
    }

    public function configure(ContainerInterface $container): void
    {
        if ($container->has(MigrationConfig::class)) {
            /** @var MigrationConfig<string,mixed> $config */
            $config = $container->get(MigrationConfig::class);
            $this->setDefault($config->isSafe());
        }
    }
}
