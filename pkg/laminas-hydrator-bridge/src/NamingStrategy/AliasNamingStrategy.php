<?php

declare(strict_types=1);

namespace spaceonfire\LaminasHydratorBridge\NamingStrategy;

use Laminas\Hydrator\NamingStrategy\NamingStrategyInterface;

final class AliasNamingStrategy implements NamingStrategyInterface
{
    /**
     * @var array<string,string>
     */
    private $aliasesMap = [];

    /**
     * AliasNamingStrategy constructor.
     * @param array<string,array<string>> $nameAliases
     */
    public function __construct(array $nameAliases = [])
    {
        foreach ($nameAliases as $name => $aliases) {
            $this->addAliases($name, $aliases);
        }
    }

    /**
     * Add multiple aliases
     * @param string $name
     * @param string[] $aliases
     * @return $this
     */
    public function addAliases(string $name, array $aliases): self
    {
        foreach ($aliases as $alias) {
            $this->addAlias($name, $alias);
        }

        return $this;
    }

    /**
     * Add alias
     * @param string $name
     * @param string $alias
     * @return $this
     */
    public function addAlias(string $name, string $alias): self
    {
        if (isset($this->aliasesMap[$alias]) && $this->aliasesMap[$alias] !== $name) {
            trigger_error(sprintf('Alias "%s" was already specified for name "%s"', $alias, $this->aliasesMap[$alias]));
        }

        $this->aliasesMap[$alias] = $name;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function hydrate(string $name, ?array $data = null): string
    {
        return $this->aliasesMap[$name] ?? $name;
    }

    /**
     * @inheritDoc
     */
    public function extract(string $name, ?object $object = null): string
    {
        return $name;
    }
}
