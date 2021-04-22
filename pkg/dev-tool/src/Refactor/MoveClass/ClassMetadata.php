<?php

declare(strict_types=1);

namespace spaceonfire\DevTool\Refactor\MoveClass;

final class ClassMetadata
{
    private const TYPE_CLASS = 0;

    private const TYPE_INTERFACE = 1;

    private const TYPE_TRAIT = 2;

    private ?string $namespace;

    private string $classname;

    private string $filepath;

    private ?int $type;

    private ?string $content = null;

    public function __construct(string $class, string $filepath, ?int $type = null)
    {
        [$this->namespace, $this->classname] = $this->splitNamespaceAndClassName($class);

        $this->filepath = $filepath;
        $this->type = $type;
    }

    public function __toString(): string
    {
        return $this->getFullClass();
    }

    public function getNamespace(): ?string
    {
        return $this->namespace;
    }

    public function getClassname(): string
    {
        return $this->classname;
    }

    public function getFullClass(): string
    {
        return '\\' . $this->namespace . '\\' . $this->classname;
    }

    public function getFilepath(): string
    {
        return $this->filepath;
    }

    public function isClass(): bool
    {
        return self::TYPE_CLASS === $this->type;
    }

    public function isInterface(): bool
    {
        return self::TYPE_INTERFACE === $this->type;
    }

    public function isTrait(): bool
    {
        return self::TYPE_TRAIT === $this->type;
    }

    public function getContent(): string
    {
        if (null === $this->content) {
            $content = \file_get_contents($this->filepath);

            \assert(\is_string($content));

            $this->setContent($content);
        }

        \assert(\is_string($this->content));

        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
        $this->type = $this->detectTypeFromContent($content);
    }

    /**
     * @param string $class
     * @return array{0: string|null, 1: string}
     */
    private function splitNamespaceAndClassName(string $class): array
    {
        $pos = \strrpos($class, '\\') ?: 0;

        $namespace = \substr($class, 0, $pos);
        $namespace = '' === $namespace ? null : $namespace;
        $classname = \substr($class, $pos + 1);

        return [$namespace, $classname];
    }

    private function detectTypeFromContent(string $content): ?int
    {
        if (\str_contains($content, 'class ' . $this->classname)) {
            return self::TYPE_CLASS;
        }

        if (\str_contains($content, 'interface ' . $this->classname)) {
            return self::TYPE_INTERFACE;
        }

        if (\str_contains($content, 'trait ' . $this->classname)) {
            return self::TYPE_TRAIT;
        }

        return null;
    }
}
