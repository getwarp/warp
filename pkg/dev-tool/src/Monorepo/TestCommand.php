<?php

declare(strict_types=1);

namespace spaceonfire\DevTool\Monorepo;

use PhpParser\Lexer\Emulative;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\FindingVisitor;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\Parser as PhpParser;
use PhpParser\ParserFactory;
use spaceonfire\DevTool\Console\ComposerHelper;
use spaceonfire\DevTool\Monorepo\Composer\ComposerJson;
use spaceonfire\DevTool\Monorepo\Composer\MonorepoConfig;
use spaceonfire\DevTool\Monorepo\Composer\MonorepoProject;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

final class TestCommand extends Command
{
    protected static $defaultName = 'monorepo:dependency';

    private Composer\ComposerJson $composer;

    private MonorepoConfig $monorepo;

    private ComposerPackagesCollation $collation;

    private PhpParser $phpParser;

    private Filesystem $filesystem;

    public function __construct(?string $name = null)
    {
        parent::__construct($name);

        $this->phpParser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7, new Emulative([
            'phpVersion' => Emulative::PHP_7_4,
        ]));

        $this->filesystem = new Filesystem();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var ComposerHelper $composerHelper */
        $composerHelper = $this->getHelper(ComposerHelper::NAME);

        $this->composer = $composerHelper->getComposerJson();
        $this->monorepo = MonorepoConfig::fromComposer($this->composer);

        $this->collation = new ComposerPackagesCollation($this->composer);

        foreach ($this->monorepo->getProjects() as $project) {
            $this->checkProject($project);
        }

        return self::SUCCESS;
    }

    private function checkProject(MonorepoProject $project): void
    {
        $monorepoDir = \dirname($this->monorepo->getFilename()) . '/';
        $projectComposer = ComposerJson::read($monorepoDir . $project->getDir() . '/composer.json');

        $srcDirs = (array)($projectComposer->getSection(ComposerJson::AUTOLOAD, [])['psr-4'] ?? []);
        $srcDirs = \array_map(static fn (string $dir) => $monorepoDir . $project->getDir() . '/' . $dir, $srcDirs);
        $srcDirs = \array_filter($srcDirs, static fn (string $dir) => \is_dir($dir));
        $testDirs = (array)($projectComposer->getSection(ComposerJson::AUTOLOAD_DEV, [])['psr-4'] ?? []);
        $testDirs = \array_map(static fn (string $dir) => $monorepoDir . $project->getDir() . '/' . $dir, $testDirs);
        $testDirs = \array_filter($testDirs, static fn (string $dir) => \is_dir($dir));

        $packages = 0 === \count($srcDirs)
            ? []
            : $this->resolveUsedPackages(
                Finder::create()
                    ->files()
                    ->name('*.php')
                    ->in($srcDirs)
            );

        $devPackages = 0 === \count($testDirs)
            ? []
            : $this->resolveUsedPackages(
                Finder::create()
                    ->files()
                    ->name('*.php')
                    ->in($testDirs)
            );

        unset($packages[$projectComposer->getName()], $devPackages[$projectComposer->getName()]);

        $devPackages = \array_diff($devPackages, $packages);

        $require = $projectComposer->getSection(ComposerJson::REQUIRE);
        foreach ($packages as $package) {
            $require[$package] = $require[$package] ?? $this->collation->getPackageVersion($package);
        }
        $projectComposer->setSection(ComposerJson::REQUIRE, $require);
        // TODO: do not add packages to require if there already in require-dev. Add them to suggest instead.

        $requireDev = $projectComposer->getSection(ComposerJson::REQUIRE_DEV);
        foreach ($devPackages as $package) {
            $requireDev[$package] = $requireDev[$package] ?? $this->collation->getPackageVersion($package);
        }
        $projectComposer->setSection(ComposerJson::REQUIRE_DEV, $requireDev);

        $this->dumpComposerJson($projectComposer);
    }

    /**
     * @param Finder $finder
     * @return array<string,string>
     */
    private function resolveUsedPackages(Finder $finder): array
    {
        $files = \iterator_to_array($finder, false);

        $names = \array_reduce($files, function (array $carry, \SplFileInfo $file) {
            \array_push($carry, ...$this->findNamesInFile($file));
            return $carry;
        }, []);
        $names = \array_unique($names);

        $dependOn = [];

        foreach ($names as $name) {
            if (null === $reflection = $this->getReflectionFor($name)) {
                continue;
            }

            $filename = $reflection->getFileName();

            if (false === $filename) {
                continue;
            }

            $dependencyPackage = $this->collation->getPackageName($filename);

            if (null === $dependencyPackage) {
                continue;
            }

            $dependOn[$dependencyPackage] = $dependencyPackage;
        }

        return $dependOn;
    }

    /**
     * @param \SplFileInfo $file
     * @return string[]
     */
    private function findNamesInFile(\SplFileInfo $file): array
    {
        $content = \file_get_contents($file->getPathname());
        \assert(\is_string($content));

        $ast = $this->phpParser->parse($content);
        \assert(null !== $ast);

        $traverser = new NodeTraverser();
        $traverser->addVisitor(new NameResolver());
        $visitor = new FindingVisitor(static fn (Node $node) => $node instanceof Node\Name\FullyQualified);
        $traverser->addVisitor($visitor);
        $traverser->traverse($ast);

        /** @var Node\Name\FullyQualified[] $nameNodes */
        $nameNodes = $visitor->getFoundNodes();
        $nameNodes = \array_reduce(
            $nameNodes,
            static function (array $carry, Node\Name\FullyQualified $node): array {
                $carry[(string)$node] ??= $node;
                return $carry;
            },
            [],
        );

        return \array_keys($nameNodes);
    }

    /**
     * @param string $name
     * @phpstan-param class-string $name
     * @return \ReflectionClass<object>|\ReflectionFunction|null
     */
    private function getReflectionFor(string $name): ?\Reflector
    {
        $reflections = [
            \ReflectionClass::class,
            \ReflectionFunction::class,
        ];

        foreach ($reflections as $reflection) {
            try {
                return new $reflection($name);
            } catch (\ReflectionException $e) {
                continue;
            }
        }

        return null;
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
