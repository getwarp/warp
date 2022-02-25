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
        if (!$this->hydrator instanceof NamingStrategyEnabledInterface || !$this->hydrator->hasNamingStrategy()) {
            return $name;
        }

        foreach ($this->splitName($name) as [$left, $right]) {
            $extracted = $this->hydrator->getNamingStrategy()->extract($left);
            if ($extracted === $left) {
                continue;
            }
            return \rtrim($extracted . '.' . $right, '.');
        }

        return $name;
    }

    /**
     * @return array<array{string,string}>
     */
    private function splitName(string $name): array
    {
        $left = $name;
        $right = '';

        $output = [];

        while (true) {
            $output[] = [$left, $right];

            $rightDot = \strrpos($left, '.');
            if (false === $rightDot) {
                break;
            }

            $right = \rtrim(\substr($left, $rightDot + 1) . '.' . $right, '.');
            $left = \substr($left, 0, $rightDot);
        }

        return $output;
    }
}
