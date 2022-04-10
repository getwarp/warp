<?php

declare(strict_types=1);

namespace Warp\Collection\Operation;

/**
 * @template K of array-key
 * @template V
 * @extends AbstractOperation<K,V,K,V>
 */
final class UniqueOperation extends AbstractOperation
{
    private const NULL = 'n';

    private const TRUE = 'b:1';

    private const FALSE = 'b:0';

    protected function generator(\Traversable $iterator): \Generator
    {
        $previouslyYielded = [];

        foreach ($iterator as $offset => $value) {
            $valueId = $this->getValueId($value);

            if (isset($previouslyYielded[$valueId])) {
                continue;
            }

            yield $offset => $value;
            $previouslyYielded[$valueId] = true;
        }
    }

    /**
     * @param mixed $value
     * @return string
     */
    private function getValueId($value): string
    {
        if (\is_object($value)) {
            return \sprintf('o:%s', \spl_object_hash($value));
        }

        if (\is_resource($value)) {
            return \sprintf('r:%s', \get_resource_id($value));
        }

        if (null === $value) {
            return self::NULL;
        }

        if (true === $value) {
            return self::TRUE;
        }

        if (false === $value) {
            return self::FALSE;
        }

        if (\is_int($value)) {
            return \sprintf('i:%x', $value);
        }

        if (\is_float($value)) {
            return \sprintf('f:%F', $value);
        }

        if (!\is_string($value)) {
            $value = \serialize($value);
        }

        return \sprintf('s:%s', \sha1($value));
    }
}
