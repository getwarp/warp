<?php

declare(strict_types=1);

namespace Warp\Bridge\LaminasHydrator\NamingStrategy;

use Laminas\Hydrator\NamingStrategy\NamingStrategyInterface;

final class AliasNamingStrategy implements NamingStrategyInterface
{
    /**
     * @var array<string,string>
     */
    private array $aliasesMap = [];

    /**
     * @param array<string,non-empty-list<string>> $nameAliases
     */
    public function __construct(array $nameAliases = [])
    {
        foreach ($nameAliases as $name => $aliases) {
            $this->addAlias($name, ...$aliases);
        }
    }

    public function addAlias(string $name, string $alias, string ...$aliases): void
    {
        foreach ([$alias, ...$aliases] as $item) {
            $this->aliasesMap[$item] = $name;
        }
    }

    public function hydrate(string $name, ?array $data = null): string
    {
        return $this->aliasesMap[$name] ?? $name;
    }

    public function extract(string $name, ?object $object = null): string
    {
        return $name;
    }
}
