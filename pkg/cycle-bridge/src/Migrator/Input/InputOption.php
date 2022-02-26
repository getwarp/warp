<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Migrator\Input;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;

/**
 * @template T
 */
class InputOption extends \Symfony\Component\Console\Input\InputOption
{
    public function register(Command $command): void
    {
        $command->getDefinition()->addOption($this);
    }

    /**
     * @return T
     */
    public function getValueFrom(InputInterface $input)
    {
        return $input->getOption($this->getName());
    }
}
