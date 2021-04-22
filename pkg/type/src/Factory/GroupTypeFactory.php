<?php

declare(strict_types=1);

namespace spaceonfire\Type\Factory;

use spaceonfire\Type\Exception\TypeNotSupportedException;
use spaceonfire\Type\TypeInterface;

final class GroupTypeFactory implements TypeFactoryInterface
{
    use TypeFactoryTrait;

    public function supports(string $type): bool
    {
        if (null === $this->parent) {
            return false;
        }

        $type = $this->removeWhitespaces($type);

        return 2 < \strlen($type)
            && \str_starts_with($type, '(')
            && \str_ends_with($type, ')')
            && $this->parent->supports(\substr($type, 1, -1));
    }

    public function make(string $type): TypeInterface
    {
        $type = $this->removeWhitespaces($type);

        if (!$this->supports($type)) {
            throw new TypeNotSupportedException($type);
        }

        \assert(null !== $this->parent);

        return $this->parent->make(\substr($type, 1, -1));
    }
}
