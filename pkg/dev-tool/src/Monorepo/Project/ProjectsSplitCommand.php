<?php

declare(strict_types=1);

namespace Warp\DevTool\Monorepo\Project;

use Gitonomy\Git\Admin;
use Gitonomy\Git\Commit;
use Gitonomy\Git\Diff\File;
use Gitonomy\Git\Exception\ProcessException;
use Gitonomy\Git\Exception\RuntimeException;
use Gitonomy\Git\Reference\Branch;
use Gitonomy\Git\Reference\Tag;
use Gitonomy\Git\Repository;
use Http\Discovery\Psr17FactoryDiscovery;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Mime\Address;
use Warp\DevTool\Console\ComposerHelper;
use Warp\DevTool\Monorepo\Composer\MonorepoConfig;
use Warp\DevTool\Monorepo\Composer\MonorepoProject;

final class ProjectsSplitCommand extends Command
{
    protected static $defaultName = 'monorepo:projects:split';

    private Filesystem $filesystem;

    public function __construct(Filesystem $filesystem, ?string $name = null)
    {
        parent::__construct($name);

        $this->filesystem = $filesystem;
    }

    protected function configure(): void
    {
        $this->addOption('allow-empty', null, InputOption::VALUE_NONE);
        $this->addOption('tmp', null, InputOption::VALUE_REQUIRED, '', \sys_get_temp_dir());
        $this->addOption('token', null, InputOption::VALUE_REQUIRED, '', \getenv('TOKEN') ?: null);
        $this->addArgument('project', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output = new SymfonyStyle($input, $output);
        /** @var ComposerHelper $composerHelper */
        $composerHelper = $this->getHelper(ComposerHelper::NAME);

        $composer = $composerHelper->getComposerJson();
        $monorepo = MonorepoConfig::fromComposer($composer);

        $rootGit = new Repository(\dirname($composer->getFilename()));

        /** @phpstan-var string $project */
        $project = $input->getArgument('project');

        $project = $this->getProject($monorepo, $project);

        $this->splitProject($rootGit, $project, $input, $output);

        return self::SUCCESS;
    }

    private function getProject(MonorepoConfig $monorepo, string $name): MonorepoProject
    {
        foreach ($monorepo->getProjects() as $project) {
            if ($project->getDir() === $name) {
                return $project;
            }
        }

        throw new \LogicException(\sprintf('Unknown project: %s', $name));
    }

    private function splitProject(
        Repository $rootGit,
        MonorepoProject $project,
        InputInterface $input,
        SymfonyStyle $output
    ): void {
        $projectDir = $rootGit->getPath() . '/' . $project->getDir();
        \assert(\is_dir($projectDir));

        $headCommit = $rootGit->getHeadCommit();
        [$pathCommit] = $rootGit->getLog(['HEAD'], [$projectDir], 0, 1)->getCommits() + [null];
        $pathCommit ??= $headCommit;

        \assert($headCommit instanceof Commit);
        \assert($pathCommit instanceof Commit);

        $headBranch = $this->findBranchByCommit($rootGit, $headCommit);
        [$headTag] = $rootGit->getReferences()->resolveTags($headCommit) + [null];

        $token = \trim((string)$input->getOption('token'));
        $tmpDir = \rtrim($input->getOption('tmp'), '/\\');
        \assert(null !== $project->getGit());
        $gitUri = Psr17FactoryDiscovery::findUriFactory()->createUri($project->getGit())->withUserInfo($token);

        $repoDir = $tmpDir . '/' . $project->getDir();
        if ($this->filesystem->exists($repoDir)) {
            throw new \RuntimeException(\sprintf('Repository directory already exists: %s.', $repoDir));
        }
        $this->filesystem->mkdir($repoDir);

        // Clone bare
        $output->text(\sprintf('Cloning %s bare repository to %s.', $project->getGit(), $repoDir));
        $repo = Admin::cloneTo($repoDir . '/.git', (string)$gitUri, true);

        // Switch branch
        $branchName = $this->getBranchName($headBranch);
        $refs = $repo->getReferences();

        if ($refs->hasBranch($branchName)) {
            $branch = $refs->getBranch($branchName);
            $repo->run('symbolic-ref', ['HEAD', $branch->getFullname()]);
            $output->text(\sprintf('Switching to existing branch: %s.', $branchName));
        } elseif (0 < $refs->count()) {
            $commit = $repo->getHeadCommit() ?? $refs->getFirstBranch()->getCommit();
            $branch = $refs->createBranch($branchName, $commit->getHash());
            $repo->run('symbolic-ref', ['HEAD', $branch->getFullname()]);
            $output->text(\sprintf('Switching to new branch: %s.', $branchName));
        } else {
            $repo->run('config', ['core.bare', 'false']);
            $repo = new Repository($repoDir);
            $repo->run('checkout', ['-b', $branchName]);
        }

        // Init working dir
        $output->text('Initializing working directory.');
        $repo->run('config', ['core.bare', 'false']);
        $repo = new Repository($repoDir);
        $this->filesystem->mirror($projectDir, $repo->getPath());

        // Commit
        $pushCommit = false;
        $repo->run('add', ['.']);
        $allowEmpty = true === $input->getOption('allow-empty');
        $diff = $repo->getWorkingCopy()->getDiffStaged();
        if ($allowEmpty || 0 < \count($diff->getFiles())) {
            if (0 < \count($diff->getFiles())) {
                $output->text('Files to commit:');
                $output->listing(\array_map(static fn (File $f) => $f->getName(), $diff->getFiles()));
            } else {
                $output->text('Empty commit will be created.');
            }

            $commitMessageFile = $this->filesystem->tempnam($tmpDir, $pathCommit->getHash());
            $this->filesystem->dumpFile($commitMessageFile, $pathCommit->getMessage());
            $repo->run('config', ['user.email', $pathCommit->getCommitterEmail()]);
            $repo->run('config', ['user.name', $pathCommit->getCommitterName()]);
            $repo->run('commit', [
                '--all',
                '--allow-empty',
                '--quiet',
                '--file',
                $commitMessageFile,
                '--author',
                $this->formatCommitAuthor($pathCommit),
                '--date',
                $pathCommit->getAuthorDate()->format(\DateTimeInterface::ATOM),
            ]);
            $this->filesystem->remove($commitMessageFile);
            $pushCommit = true;
        }

        // Tag
        $pushTag = false;
        if (null !== $headTag) {
            if ($refs->hasTag($headTag->getName())) {
                $output->warning(\sprintf('Skipping tag creation. Tag %s already exists.', $headTag->getName()));
            } else {
                $commit = $repo->getHeadCommit();
                \assert($commit instanceof Commit);
                $refs->createTag($headTag->getName(), $commit->getHash());
                $output->text(\sprintf('Tag created: %s.', $headTag->getName()));
                $pushTag = true;
            }
        }

        // Push
//        $pushCommit = $pushTag = false;
        if ($pushCommit || $pushTag) {
            $output->text('Pushing changes.');
            $repo->run('push', ['--quiet', '--tags', '--set-upstream', 'origin', $branchName]);
        } else {
            $output->warning('Nothing to push.');
        }

        // Cleanup
        $this->filesystem->remove($repo->getPath());

        $output->success('Project split done.');
    }

    private function findBranchByCommit(Repository $git, Commit $commit): Branch
    {
        [$headBranch] = $git->getReferences()->resolveBranches($commit) + [null];

        if (null !== $headBranch) {
            return $headBranch;
        }

        $defaultBranch = $git->getReferences()->getFirstBranch();
        \assert($defaultBranch instanceof Branch);

        $includingBranches = [];
        $bestMatches = [];

        foreach ($this->getIncludingBranches($commit) as $branch) {
            $includingBranches[] = $branch;

            if ($branch === $defaultBranch) {
                $bestMatches[] = $branch;
            }

            if (1 === \preg_match('/^[\d]+\.[\d]+(\.[\dx]+)?/', $this->getBranchName($branch))) {
                $bestMatches[] = $branch;
            }
        }

        /** @var Branch[] $bestMatches */
        $bestMatches = 0 < \count($bestMatches) ? $bestMatches : $includingBranches;

        // get earlier match
        $match = null;
        foreach ($bestMatches as $branch) {
            $match ??= $branch;

            $branchDate = $branch->getCommit()->getCommitterDate();
            $matchDate = $match->getCommit()->getCommitterDate();

            if ($branchDate < $matchDate) {
                $match = $branch;
            }
        }

        if (null !== $match) {
            return $match;
        }

        throw new \RuntimeException(\sprintf('Cannot find branch by commit %s.', $commit->getHash()));
    }

    private function getBranchName(Branch $branch): string
    {
        $fullname = $branch->getFullname();

        if (\preg_match('#^refs/heads/(?<name>.*)$#', $fullname, $vars)) {
            return $vars['name'];
        }

        if (\preg_match('#^refs/remotes/(?<remote>[^/]*)/(?<name>.*)$#', $fullname, $vars)) {
            return $vars['name'];
        }

        throw new RuntimeException(\sprintf('Cannot extract branch name from "%s"', $fullname));
    }

    private function formatCommitAuthor(Commit $commit): string
    {
        $addr = new Address($commit->getAuthorEmail(), $commit->getAuthorName());
        return $addr->toString();
    }

    /**
     * @param Commit $commit
     * @return \Generator<Branch>
     */
    private function getIncludingBranches(Commit $commit): \Generator
    {
        try {
            $result = $commit->getRepository()->run('branch', ['--contains', $commit->getHash()]);
        } catch (ProcessException $e) {
            return yield from [];
        }

        $rows = \explode("\n", $result);

        $references = $commit->getRepository()->getReferences();

        foreach ($rows as $row) {
            $row = \trim(\str_replace('*', '', $row));

            if ('' === $row) {
                continue;
            }

            try {
                // skip detached state
                $commit->getRepository()->run('check-ref-format', ['--branch', $row]);
            } catch (ProcessException $e) {
                continue;
            }

            yield $references->getBranch($row);
        }
    }
}
