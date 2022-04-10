<?php

declare(strict_types=1);

namespace Warp\Collection\Operation;

use Warp\Common\Field\FieldInterface;

/**
 * @template K of array-key
 * @template V
 * @extends AbstractOperation<K,V,K,V>
 */
final class IndexByOperation extends AbstractOperation
{
    /**
     * @var callable(V):K
     */
    private $keyExtractor;

    /**
     * @param FieldInterface|callable(V):K $keyExtractor
     */
    public function __construct($keyExtractor)
    {
        parent::__construct(true);

        $this->keyExtractor = $this->prepareKeyExtractor($keyExtractor);
    }

    protected function generator(\Traversable $iterator): \Generator
    {
        /** @var V $element */
        foreach ($iterator as $element) {
            yield ($this->keyExtractor)($element) => $element;
        }
    }

    /**
     * @param FieldInterface|callable(V):K|mixed $keyExtractor
     * @return callable(V):K
     */
    private function prepareKeyExtractor($keyExtractor): callable
    {
        if ($keyExtractor instanceof FieldInterface) {
            return static function ($element) use ($keyExtractor) {
                $offset = $keyExtractor->extract($element);

                if (\is_object($offset) && \method_exists($offset, '__toString')) {
                    return (string)$offset;
                }

                return $offset;
            };
        }

        if (\is_callable($keyExtractor)) {
            return $keyExtractor;
        }

        throw new \LogicException(\sprintf(
            'Expected $keyExtractor to be callable or instance of %s. Got: %s.',
            FieldInterface::class,
            \get_debug_type($keyExtractor),
        ));
    }
}
