<?php

declare(strict_types=1);

namespace Warp\DevTool\Monorepo\Composer;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Warp\DevTool\Console\ComposerHelper;
use Warp\DevTool\Monorepo\Composer\Synchronizer\ComposerJsonSynchronizer;
use Warp\DevTool\Monorepo\Composer\Synchronizer\VersionConflict;

final class MonorepoComposerCommand extends Command
{
    protected static $defaultName = 'monorepo:composer';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);

        if (!$input->isInteractive()) {
            $style->error(\sprintf('Command %s cannot be run in non-interactive way.', $this->getName()));
            return self::FAILURE;
        }

        /** @var ComposerHelper $composerHelper */
        $composerHelper = $this->getHelper(ComposerHelper::NAME);

        $synchronizer = new ComposerJsonSynchronizer($composerHelper->getComposerJson());

        foreach ($synchronizer->resolveConflicts() as $conflict) {
            $this->resolveConflict($conflict, $style);
        }

        $synchronizer->dump();

        return self::SUCCESS;
    }

    private function resolveConflict(VersionConflict $conflict, SymfonyStyle $style): void
    {
        $style->warning($conflict->getMessage());
        $style->table(
            ['Project', 'Version'],
            \array_map(
                static fn ($project, $version) => [$project, $version],
                \array_keys($conflict->getOptions()),
                \array_values($conflict->getOptions()),
            ),
        );
        $chosenVersion = $style->choice($conflict->getMessage(), \array_values(\array_unique($conflict->getOptions())));

        try {
            $conflict->resolve($chosenVersion);
        } catch (\InvalidArgumentException | \UnexpectedValueException $e) {
            $style->error($e->getMessage());
            $this->resolveConflict($conflict, $style);
        }
    }
}
