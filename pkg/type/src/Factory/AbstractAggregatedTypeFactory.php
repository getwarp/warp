<?php

declare(strict_types=1);

namespace spaceonfire\Type\Factory;

abstract class AbstractAggregatedTypeFactory implements TypeFactoryInterface
{
    use TypeFactoryTrait;

    /**
     * @var non-empty-string
     */
    protected $delimiter;

    /**
     * @param non-empty-string $delimiter
     */
    public function __construct(string $delimiter)
    {
        $this->delimiter = $delimiter;
    }

    /**
     * @inheritDoc
     */
    final public function supports(string $type): bool
    {
        return null !== $this->parse($type);
    }

    final protected function parse(string $type): ?array
    {
        if (null === $this->parent) {
            return null;
        }

        $type = $this->removeWhitespaces($type);

        [$left, $right] = $this->split($type);

        if ('' === $left || '' === $right) {
            return null;
        }

        while (!$this->parent->supports($left)) {
            [$appendLeft, $right] = $this->split($right);

            if ('' !== $appendLeft) {
                $left .= $this->delimiter . $appendLeft;
            }

            if ('' === $right) {
                break;
            }
        }

        if (!$this->parent->supports($right) || !$this->parent->supports($left)) {
            return null;
        }

        return [$left, $right];
    }

    private function split(string $string): array
    {
        return explode($this->delimiter, $string, 2) + ['', ''];
    }
}
