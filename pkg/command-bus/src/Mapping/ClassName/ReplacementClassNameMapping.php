<?php

declare(strict_types=1);

namespace spaceonfire\CommandBus\Mapping\ClassName;

final class ReplacementClassNameMapping implements ClassNameMappingInterface
{
    /**
     * @var string[]
     */
    private array $search;

    /**
     * @var string[]
     */
    private array $replace;

    /**
     * @param string|string[] $search
     * @param string|string[]|null $replace
     */
    public function __construct($search, $replace = null)
    {
        if (null === $replace && \is_array($search)) {
            $replace = \array_values($search);
            $search = \array_keys($search);
        }

        $search = \is_array($search) ? $search : [$search];
        $replace = \is_array($replace) ? $replace : \array_fill(0, \count($search), $replace);

        $cast = static fn ($v) => (string)$v;

        $this->search = \array_map($cast, $search);
        $this->replace = \array_map($cast, $replace);
    }

    public function getClassName(string $commandClass): string
    {
        return \str_replace($this->search, $this->replace, $commandClass);
    }
}
