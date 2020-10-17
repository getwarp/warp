<?php

declare(strict_types=1);

namespace spaceonfire\Type\Factory;

abstract class AbstractAggregatedTypeFactory implements TypeFactoryInterface
{
    use TypeFactoryTrait;

    /**
     * @var string
     */
    protected $delimiter;

    public function __construct(string $delimiter)
    {
        $this->delimiter = $delimiter;
    }

    private function split(string $string): array
    {
        return (explode($this->delimiter, $string, 2) ?: []) + ['', ''];
    }

    final protected function parse(string $type): ?array
    {
        if ($this->parent === null) {
            return null;
        }

        $type = $this->removeWhitespaces($type);

        [$left, $right] = $this->split($type);

        if ($left === '' || $right === '') {
            return null;
        }

        while (!$this->parent->supports($left)) {
            [$appendLeft, $right] = $this->split($right);

            if ($appendLeft !== '') {
                $left .= $this->delimiter . $appendLeft;
            }

            if ($right === '') {
                break;
            }
        }

        if (!$this->parent->supports($right) || !$this->parent->supports($left)) {
            return null;
        }

        return [$left, $right];
    }

    /**
     * @inheritDoc
     */
    final public function supports(string $type): bool
    {
        return $this->parse($type) !== null;
    }
}
