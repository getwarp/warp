<?php

declare(strict_types=1);

namespace Warp\DevTool\ChangeLog;

use Gitonomy\Git\Commit;
use Gitonomy\Git\Log;
use Gitonomy\Git\Repository;
use Gitonomy\Git\Revision;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

final class ChangeLogCommand extends Command
{
    protected static $defaultName = 'changelog';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $cwd = \getcwd();

        \assert(\is_string($cwd));

        $git = new Repository($cwd);

        $paths = [
            'src/Collection',
            'src/CommandBus',
            'src/Common',
            'src/Container',
            'src/Criteria',
            'src/DataSource',
            'src/EasyCodingStandardBridge',
            'src/LaminasHydratorBridge',
            'src/MonologBridge',
            'src/Type',
            'src/ValueObject',
        ];

        $fs = new Filesystem();

        foreach ($paths as $path) {
            $path = $cwd . '/' . $path;
            $changelog = $path . '/CHANGELOG.md';

            if (!\file_exists($changelog)) {
                continue;
            }

            /** @var Commit $pathCommit */
            $pathCommit = $git->getLog(['HEAD'], [$path], 0, 1)->getSingleCommit();
            /** @var Commit $changelogCommit */
            $changelogCommit = $git->getLog(['HEAD'], [$changelog], 0, 1)->getSingleCommit();

            if ($changelogCommit->getHash() === $pathCommit->getHash()) {
                continue;
            }

            $rev = new Revision($git, \sprintf('%s..%s', $changelogCommit->getHash(), $pathCommit->getHash()));

            $text = $this->getChangelogSection($rev->getLog($path));

            if (null === $text) {
                continue;
            }

            $changelogContent = \file_get_contents($changelog);

            \assert(\is_string($changelogContent));

            $pos = \strpos($changelogContent, '## [');

            if (!$pos) {
                $changelogContent = $text . \PHP_EOL . \PHP_EOL . $changelogContent;
            } else {
                $before = \trim(\substr($changelogContent, 0, $pos));
                $after = \trim(\substr($changelogContent, $pos));

                $changelogContent = $before . \PHP_EOL . \PHP_EOL . $text . \PHP_EOL . \PHP_EOL . $after;
            }

            $fs->dumpFile($changelog, $changelogContent);
        }

        return self::SUCCESS;
    }

    /**
     * @param Log<Commit> $log
     * @return string|null
     */
    private function getChangelogSection(Log $log): ?string
    {
        /** @var Commit[] $commits */
        $commits = $log->getCommits();

        if (0 === \count($commits)) {
            return null;
        }

        $featureCommits = [];
        $refactorCommits = [];
        $fixesCommits = [];
        $otherCommits = [];

        foreach ($commits as $commit) {
            $message = \sprintf('(%s) %s', $commit->getShortHash(), $commit->getSubjectMessage());

            if (\str_starts_with($commit->getSubjectMessage(), 'feat')) {
                $featureCommits[] = $message;
                continue;
            }

            if (\str_starts_with($commit->getSubjectMessage(), 'refactor')) {
                $refactorCommits[] = $message;
                continue;
            }

            if (\str_starts_with($commit->getSubjectMessage(), 'fix')) {
                $fixesCommits[] = $message;
                continue;
            }

            $otherCommits[] = $message;
        }

        $now = new \DateTimeImmutable();

        $output = [];

        $output[] = \sprintf('## [X.Y.Z] - %s', $now->format('Y-m-d'));

        $output[] = '';
        $output[] = '### Added';
        $output[] = '';
        foreach ($featureCommits as $message) {
            $output[] = '-   ' . $message;
        }
        foreach ($refactorCommits as $message) {
            $output[] = '-   ' . $message;
        }
        if ([] === $featureCommits && [] === $refactorCommits) {
            $output[] = '-   Nothing';
        }

        $output[] = '';
        $output[] = '### Deprecated';
        $output[] = '';
        $output[] = '-   Nothing';

        $output[] = '';
        $output[] = '### Fixed';
        $output[] = '';
        foreach ($fixesCommits as $message) {
            $output[] = '-   ' . $message;
        }
        if ([] === $fixesCommits) {
            $output[] = '-   Nothing';
        }

        $output[] = '';
        $output[] = '### Removed';
        $output[] = '';
        $output[] = '-   Nothing';

        $output[] = '';
        $output[] = '### Security';
        $output[] = '';
        $output[] = '-   Nothing';

        if ([] !== $otherCommits) {
            $output[] = '';
            $output[] = '#### Other commits';
            $output[] = '';
            foreach ($otherCommits as $message) {
                $output[] = '-   ' . $message;
            }
        }

        return \implode(\PHP_EOL, $output);
    }
}
