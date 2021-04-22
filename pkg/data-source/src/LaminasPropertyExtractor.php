<?php

declare(strict_types=1);

namespace spaceonfire\DataSource;

use Laminas\Hydrator\HydratorInterface;
use Laminas\Hydrator\NamingStrategy\NamingStrategyEnabledInterface;
use Laminas\Hydrator\Strategy\StrategyEnabledInterface;

final class LaminasPropertyExtractor implements PropertyExtractorInterface
{
    private HydratorInterface $hydrator;

    public function __construct(HydratorInterface $hydrator)
    {
        $this->hydrator = $hydrator;
    }

    public function extractValue(string $name, $value)
    {
        if ($this->hydrator instanceof StrategyEnabledInterface && $this->hydrator->hasStrategy($name)) {
            return $this->hydrator->getStrategy($name)->extract($value);
        }

        return $value;
    }

    public function extractName(string $name): string
    {
        if ($this->hydrator instanceof NamingStrategyEnabledInterface && $this->hydrator->hasNamingStrategy()) {
            return $this->hydrator->getNamingStrategy()->extract($name);
        }

        return $name;
    }
}
