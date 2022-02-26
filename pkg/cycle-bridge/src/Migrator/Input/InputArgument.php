<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Migrator\Input;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;

/**
 * @template T
 */
class InputArgument extends \Symfony\Component\Console\Input\InputArgument
{
    public function register(Command $command): void
    {
        $command->getDefinition()->addArgument($this);
    }

    /**
     * @return T
     */
    public function getValueFrom(InputInterface $input)
    {
        return $input->getArgument($this->getName());
    }
}
