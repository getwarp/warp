<?php

declare(strict_types=1);

namespace Warp\DevTool\Refactor\MoveClass;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class MoveClassCommand extends Command
{
    protected static $defaultName = 'refactor:class:move';

    private MoveClassRefactor $refactor;

    public function __construct(MoveClassRefactor $refactor, ?string $name = null)
    {
        parent::__construct($name);

        $this->refactor = $refactor;
    }

    /**
     * @inheritDoc
     */
    protected function configure(): void
    {
        $this->addArgument('from', InputArgument::REQUIRED);
        $this->addArgument('to', InputArgument::REQUIRED);
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string $from */
        $from = $input->getArgument('from');
        $from = \trim($from, '\\');

        /** @var string $to */
        $to = $input->getArgument('to');
        $to = \trim($to, '\\');

        $this->refactor->run($from, $to);

        return self::SUCCESS;
    }
}
