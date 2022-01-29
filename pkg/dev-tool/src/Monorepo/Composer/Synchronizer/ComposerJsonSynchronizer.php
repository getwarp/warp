<?php

declare(strict_types=1);

namespace spaceonfire\DevTool\Monorepo\Composer\Synchronizer;

use Composer\Semver\VersionParser;
use spaceonfire\DevTool\Monorepo\Composer\ComposerJson;
use spaceonfire\DevTool\Monorepo\Composer\MonorepoConfig;
use spaceonfire\DevTool\Monorepo\Composer\MonorepoProject;
use Symfony\Component\Filesystem\Filesystem;

final class ComposerJsonSynchronizer
{
    private ComposerJson $rootComposer;

    private string $monorepoDir;

    /**
     * @var \SplObjectStorage<MonorepoProject,ComposerJson>
     */
    private \SplObjectStorage $projects;

    private VersionParser $versionParser;

    private Filesystem $filesystem;

    /**
     * @var array<string,array<string,true>>
     */
    private array $branchAlias = [];

    /**
     * @var array<string,array<string,true>>
     */
    private array $require = [];

    /**
     * @var array<string,array<string,true>>
     */
    private array $requireDev = [];

    /**
     * @var string[]
     */
    private array $bin = [];

    /**
     * @var array<string,mixed>
     */
    private array $autoload = [];

    /**
     * @var array<string,mixed>
     */
    private array $autoloadDev = [];

    public function __construct(ComposerJson $rootComposer)
    {
        $this->rootComposer = $rootComposer;
        $this->projects = new \SplObjectStorage();
        $this->versionParser = new VersionParser();
        $this->filesystem = new Filesystem();

        $this->monorepoDir = \dirname($rootComposer->getFilename()) . '/';

        $monorepoConfig = MonorepoConfig::fromComposer($this->rootComposer);

        /**
         * @phpstan-var string $package
         * @phpstan-var string $version
         */
        foreach ($monorepoConfig->getSection(MonorepoConfig::REQUIRE, []) as $package => $version) {
            $this->require[$package] ??= [];
            $this->require[$package][$version] = true;
        }

        /**
         * @phpstan-var string $package
         * @phpstan-var string $version
         */
        foreach ($monorepoConfig->getSection(MonorepoConfig::REQUIRE_DEV, []) as $package => $version) {
            $this->requireDev[$package] ??= [];
            $this->requireDev[$package][$version] = true;
        }

        foreach ($monorepoConfig->getProjects() as $project) {
            $this->addProject($project);
        }

        $this->normalize();
    }

    /**
     * @return \Generator<VersionConflict>
     */
    public function resolveConflicts(): \Generator
    {
        if (1 < \count($this->branchAlias)) {
            $options = [];

            foreach ($this->branchAlias as $alias => $packages) {
                foreach ($packages as $package => $_) {
                    $options[$package] = $alias;
                }
            }

            yield new VersionConflict(
                'Multiple branch aliases declared. Choose which is correct.',
                $options,
                function (string $value): void {
                    $value = $this->versionParser->normalize($value);
                    $alias = $this->versionParser->parseNumericAliasPrefix($value);

                    if (false === $alias) {
                        throw new \InvalidArgumentException();
                    }

                    $alias = \trim($alias, '.');

                    [$major, $minor] = \explode('.', $alias, 3) + ['0', '0'];
                    $shortAlias = $major . '.' . $minor;
                    $value = \str_replace($alias, $shortAlias, $value);
                    $alias = $shortAlias;

                    $this->branchAlias = [];

                    foreach ($this->projects as $project) {
                        /** @var ComposerJson $projectComposer */
                        $projectComposer = $this->projects->offsetGet($project);
                        $this->branchAlias[$value][$projectComposer->getName()] = true;

                        $this->require[$projectComposer->getName()] = [
                            '^' . $alias => true,
                        ];
                    }
                }
            );
        }

        foreach ($this->require as $package => $versions) {
            if (1 === \count($versions)) {
                continue;
            }

            $options = [];

            foreach ($this->projects as $project) {
                /** @var ComposerJson $projectComposer */
                $projectComposer = $this->projects->offsetGet($project);

                $version = $projectComposer->getSection(ComposerJson::REQUIRE, [])[$package]
                    ?? $projectComposer->getSection(ComposerJson::REQUIRE_DEV, [])[$package]
                    ?? null;

                if (null === $version) {
                    continue;
                }

                $options[$projectComposer->getName()] = $version;
            }

            yield new VersionConflict(
                \sprintf('Dependency %s found in multiple versions. Choose which is correct.', $package),
                $options,
                function (string $value) use ($package): void {
                    $this->require[$package] = [
                        $value => true,
                    ];
                }
            );
        }

        foreach ($this->requireDev as $package => $versions) {
            if (1 === \count($versions)) {
                continue;
            }

            $options = [];

            foreach ($this->projects as $project) {
                /** @var ComposerJson $projectComposer */
                $projectComposer = $this->projects->offsetGet($project);

                $version = $projectComposer->getSection(ComposerJson::REQUIRE_DEV, [])[$package] ?? null;

                if (null === $version) {
                    continue;
                }

                $options[$projectComposer->getName()] = $version;
            }

            yield new VersionConflict(
                \sprintf('Development dependency %s found in multiple versions. Choose which is correct.', $package),
                $options,
                function (string $value) use ($package): void {
                    $this->requireDev[$package] = [
                        $value => true,
                    ];
                }
            );
        }
    }

    public function dump(): void
    {
        $require = [];
        foreach ($this->require as $package => $versions) {
            $version = \array_key_first($versions);
            \assert(\is_string($version));
            $require[$package] = $version;
        }
        $require = $this->sortRequireList($require);

        $requireDev = [];
        foreach ($this->requireDev as $package => $versions) {
            $version = \array_key_first($versions);
            \assert(\is_string($version));
            $requireDev[$package] = $version;
        }
        $requireDev = $this->sortRequireList($requireDev);

        $this->rootComposer->setSection(ComposerJson::BIN, $this->bin);
        $this->rootComposer->setSection(ComposerJson::AUTOLOAD, $this->autoload);
        $this->rootComposer->setSection(ComposerJson::AUTOLOAD_DEV, $this->autoloadDev);

//        $extra = $this->rootComposer->getSection(ComposerJson::EXTRA, []);
//        $extra['branch-alias'] ??= [];
//
//        $branch = \array_key_first($extra['branch-alias']) ?? 'dev-master';
//        $branchAlias = \array_key_first($this->branchAlias);
//
//        $extra['branch-alias'][$branch] = $branchAlias;
//
//        $this->rootComposer->setSection(ComposerJson::EXTRA, $extra);

        $replace = [];

        foreach ($this->projects as $project) {
            /** @var ComposerJson $projectComposer */
            $projectComposer = $this->projects->offsetGet($project);

            $projectRequire = $projectComposer->getSection(ComposerJson::REQUIRE, []);
            foreach ($projectRequire as $package => $_) {
                $projectRequire[$package] = $require[$package] ?? $requireDev[$package];
            }
            $projectRequire = $this->sortRequireList($projectRequire);
            $projectComposer->setSection(ComposerJson::REQUIRE, $projectRequire);

            $projectRequireDev = $projectComposer->getSection(ComposerJson::REQUIRE_DEV, []);
            foreach ($projectRequireDev as $package => $_) {
                $projectRequireDev[$package] = $require[$package] ?? $requireDev[$package];
            }
            $projectRequireDev = $this->sortRequireList($projectRequireDev);
            $projectComposer->setSection(ComposerJson::REQUIRE_DEV, $projectRequireDev);

//            $projectExtra = $projectComposer->getSection(ComposerJson::EXTRA, []);
//            $projectExtra['branch-alias'] ??= [];
//            $projectBranch = \array_key_first($projectExtra['branch-alias']) ?? 'dev-master';
//            $projectExtra['branch-alias'][$projectBranch] = $branchAlias;
//            $projectComposer->setSection(ComposerJson::EXTRA, $projectExtra);

            $replace[$projectComposer->getName()] = 'self.version';

            $this->dumpComposerJson($projectComposer);
        }

        foreach ($this->projects as $project) {
            /** @var ComposerJson $projectComposer */
            $projectComposer = $this->projects->offsetGet($project);
            unset($require[$projectComposer->getName()], $requireDev[$projectComposer->getName()]);
        }

        $this->rootComposer->setSection(ComposerJson::REQUIRE, $require);
        $this->rootComposer->setSection(ComposerJson::REQUIRE_DEV, $requireDev);

        $this->rootComposer->setSection(ComposerJson::REPLACE, $replace);

        $this->dumpComposerJson($this->rootComposer);
    }

    private function addProject(MonorepoProject $project): void
    {
        $projectComposer = ComposerJson::read($this->monorepoDir . $project->getDir() . '/composer.json');

        $this->projects->attach($project, $projectComposer);

        /**
         * @phpstan-var string $package
         * @phpstan-var string $version
         */
        foreach ($projectComposer->getSection(ComposerJson::REQUIRE, []) as $package => $version) {
            $this->require[$package] ??= [];
            $this->require[$package][$version] = true;
        }

        /**
         * @phpstan-var string $package
         * @phpstan-var string $version
         */
        foreach ($projectComposer->getSection(ComposerJson::REQUIRE_DEV, []) as $package => $version) {
            $this->requireDev[$package] ??= [];
            $this->requireDev[$package][$version] = true;
        }

        foreach ($projectComposer->getSection(ComposerJson::BIN, []) as $binary) {
            $this->bin[] = $project->getDir() . '/' . $binary;
        }

        $this->autoload = $this->mergeAutoload(
            $this->autoload,
            $project,
            $projectComposer->getSection(ComposerJson::AUTOLOAD, [])
        );
        $this->autoloadDev = $this->mergeAutoload(
            $this->autoloadDev,
            $project,
            $projectComposer->getSection(ComposerJson::AUTOLOAD_DEV, [])
        );

        $extra = $projectComposer->getSection(ComposerJson::EXTRA, []);
        /** @var array<string,string> $branchAlias */
        $branchAlias = $extra['branch-alias'] ?? [];
        $branch = isset($branchAlias['dev-master']) ? 'dev-master' : \array_key_first($branchAlias);
        $alias = null === $branch ? 'null' : $branchAlias[$branch];
        $this->branchAlias[$alias][$projectComposer->getName()] = true;
    }

    /**
     * @param array<string,mixed> $autoload
     * @param MonorepoProject $project
     * @param array<string,mixed> $projectAutoload
     * @return array<string,mixed>
     */
    private function mergeAutoload(array $autoload, MonorepoProject $project, array $projectAutoload): array
    {
        $output = $autoload;

        foreach ($projectAutoload as $type => $rules) {
            switch ($type) {
                case 'psr-4':
                    $output['psr-4'] ??= [];

                    foreach ($rules as $namespace => $dirs) {
                        $output['psr-4'][$namespace] = (array)($output['psr-4'][$namespace] ?? []);

                        foreach ((array)$dirs as $dir) {
                            $output['psr-4'][$namespace][] = $project->getDir() . '/' . $dir;
                        }
                    }
                    break;

                case 'files':
                    $output['files'] ??= [];

                    foreach ($rules as $file) {
                        $output['files'][] = $project->getDir() . '/' . $file;
                    }
                    break;

                default:
                    throw new \RuntimeException(\sprintf('Autoload type %s not supported.', $type));
            }
        }

        return $output;
    }

    private function normalize(): void
    {
        foreach ($this->requireDev as $package => $versions) {
            if (!isset($this->require[$package])) {
                continue;
            }

            foreach ($versions as $version => $_) {
                $this->require[$package][$version] = true;
            }

            unset($this->requireDev[$package]);
        }

        $this->bin = \array_unique($this->bin);

        $this->autoload = $this->normalizeAutoload($this->autoload);
        $this->autoloadDev = $this->normalizeAutoload($this->autoloadDev);
    }

    /**
     * @param array<string,mixed> $autoload
     * @return array<string,mixed>
     */
    private function normalizeAutoload(array $autoload): array
    {
        $output = [];

        foreach ($autoload as $type => $rules) {
            switch ($type) {
                case 'psr-4':
                    $output['psr-4'] = [];

                    foreach ($rules as $namespace => $dirs) {
                        $dirs = (array)$dirs;
                        $output['psr-4'][$namespace] = 1 === \count($dirs) ? $dirs[0] : $dirs;
                    }
                    break;

                case 'files':
                    $output['files'] = \array_unique($rules);
                    break;

                default:
                    throw new \RuntimeException(\sprintf('Autoload type %s not supported.', $type));
            }
        }

        return $output;
    }

    /**
     * @param array<string,string> $require
     * @return array<string,string>
     */
    private function sortRequireList(array $require): array
    {
        \uksort($require, static function ($a, $b) {
            if ('php' === $a) {
                return -1;
            }

            if ('php' === $b) {
                return 1;
            }

            if (0 === \strpos($a, 'ext-')) {
                return -1;
            }

            if (0 === \strpos($b, 'ext-')) {
                return 1;
            }

            return \strcasecmp($a, $b);
        });
        return $require;
    }

    private function dumpComposerJson(ComposerJson $composerJson): void
    {
        $content = \json_encode(
            $composerJson,
            \JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE | \JSON_THROW_ON_ERROR
        );

//        $this->filesystem->rename($composerJson->getFilename(), $composerJson->getFilename() . '.bak', true);
        $this->filesystem->dumpFile($composerJson->getFilename(), $content . \PHP_EOL);
    }
}
