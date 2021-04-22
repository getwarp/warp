<?php

declare(strict_types=1);

namespace spaceonfire\DevTool\Refactor\MoveClass;

use Composer\Autoload\ClassLoader;
use Symfony\Component\Filesystem\Filesystem;

final class MoveClassRefactor
{
    private ClassLoader $classLoader;

    private Filesystem $filesystem;

    public function __construct(ClassLoader $classLoader, Filesystem $filesystem)
    {
        $this->classLoader = $classLoader;
        $this->filesystem = $filesystem;
    }

    public function run(string $from, string $to): void
    {
        $fromClass = $this->prepareFrom($from);
        $toClass = $this->prepareTo($to);

        $this->renameContent($fromClass, $toClass);
        $this->generateBackwardCompatibilityAlias($fromClass, $toClass);

        $this->syncChanges($fromClass, $toClass);
    }

    private function prepareFrom(string $class): ClassMetadata
    {
        $filename = $this->classLoader->findFile($class);

        if (!$filename) {
            throw new \RuntimeException(\sprintf('Unknown class: "%s"', $class));
        }

        return new ClassMetadata($class, $filename);
    }

    private function prepareTo(string $class): ClassMetadata
    {
        foreach ($this->classLoader->getPrefixesPsr4() as $prefix => $dirs) {
            if (!\str_starts_with($class, $prefix)) {
                continue;
            }

            $classWithoutPrefix = \trim(\str_replace($prefix, '', $class), '\\');
            $filename = \sprintf('%s/%s.php', $dirs[0], \str_replace('\\', '/', $classWithoutPrefix));

            if ($this->filesystem->exists($filename)) {
                throw new \RuntimeException(\sprintf('File already exists: "%s"', $filename));
            }

            return new ClassMetadata($class, $filename);
        }

        throw new \RuntimeException(\sprintf('Cannot define where class "%s" should be located.', $class));
    }

    private function renameContent(ClassMetadata $from, ClassMetadata $to): void
    {
        // TODO: operate code with AST
        $fromContent = $from->getContent();
        $toContent = \str_replace(
            [
                'namespace ' . $from->getNamespace(),
                'final class ' . $from->getClassname(),
                'class ' . $from->getClassname(),
                'interface ' . $from->getClassname(),
                'trait ' . $from->getClassname(),
            ],
            [
                'namespace ' . $to->getNamespace(),
                'class ' . $to->getClassname(),
                'class ' . $to->getClassname(),
                'interface ' . $to->getClassname(),
                'trait ' . $to->getClassname(),
            ],
            $fromContent
        );

        if ($toContent === $fromContent) {
            throw new \RuntimeException('Cannot process changes.');
        }

        $to->setContent($toContent);
    }

    private function generateBackwardCompatibilityAlias(ClassMetadata $from, ClassMetadata $to): void
    {
        if ($to->isClass()) {
            $code = <<<TEXT
                class {$from->getClassname()} extends {$to->getFullClass()}
                {
                }
            TEXT;
        } elseif ($to->isInterface()) {
            $code = <<<TEXT
                interface {$from->getClassname()} extends {$to->getFullClass()}
                {
                }
            TEXT;
        } elseif ($to->isTrait()) {
            $code = <<<TEXT
                trait {$from->getClassname()}
                {
                    use {$to->getFullClass()};
                }
            TEXT;
        } else {
            throw new \RuntimeException('Element type not detected.');
        }

        $content = <<<PHP
<?php

declare(strict_types=1);

namespace {$from->getNamespace()};

\class_alias(
    {$to->getFullClass()}::class,
    __NAMESPACE__ . '\\{$from->getClassname()}'
);

if (false) {
    /**
     * @deprecated Use {@see {$to->getFullClass()}} instead.
     */
{$code}
}
PHP;

        $from->setContent($content);
    }

    private function syncChanges(ClassMetadata $fromClass, ClassMetadata $toClass): void
    {
        $this->filesystem->dumpFile($fromClass->getFilepath(), $fromClass->getContent());
        $this->filesystem->dumpFile($toClass->getFilepath(), $toClass->getContent());
    }
}
