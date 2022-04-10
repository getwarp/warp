<?php

declare(strict_types=1);

namespace Warp\DevTool\Monorepo\Project;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Warp\DevTool\Console\ComposerHelper;
use Warp\DevTool\Monorepo\Composer\MonorepoConfig;

final class ProjectsListCommand extends Command
{
    protected static $defaultName = 'monorepo:projects';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var ComposerHelper $composerHelper */
        $composerHelper = $this->getHelper(ComposerHelper::NAME);

        $composer = $composerHelper->getComposerJson();
        $monorepo = MonorepoConfig::fromComposer($composer);

        $output->writeln(\json_encode([...$monorepo->getProjects()], \JSON_THROW_ON_ERROR));

        return self::SUCCESS;
    }
}
