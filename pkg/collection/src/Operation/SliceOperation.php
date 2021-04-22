<?php

declare(strict_types=1);

namespace spaceonfire\Collection\Operation;

/**
 * @template K of array-key
 * @template V
 * @extends AbstractOperation<K,V,K,V>
 */
final class SliceOperation extends AbstractOperation
{
    private int $offset;

    private ?int $limit;

    /**
     * @param int $offset
     * @param int|null $limit
     * @param bool $preserveKeys
     */
    public function __construct(int $offset, ?int $limit, bool $preserveKeys = false)
    {
        parent::__construct($preserveKeys);

        if (0 > $offset) {
            throw new \InvalidArgumentException(\sprintf(
                'Expected $offset to be greater than or equal to 0. Got: %d.',
                $offset
            ));
        }

        if (null !== $limit && 1 > $limit) {
            throw new \InvalidArgumentException(\sprintf('Expected $limit to be positive. Got: %d.', $limit));
        }

        $this->offset = $offset;
        $this->limit = $limit;
    }

    protected function generator(\Traversable $iterator): \Generator
    {
        $offset = $this->offset;
        $limit = $this->limit;

        foreach ($iterator as $index => $value) {
            if (0 < $offset) {
                $offset--;
                continue;
            }

            yield $index => $value;

            if (null === $limit) {
                continue;
            }

            $limit--;

            if (0 === $limit) {
                break;
            }
        }
    }
}
