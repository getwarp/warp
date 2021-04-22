<?php

declare(strict_types=1);

namespace spaceonfire\Type\Factory;

use spaceonfire\Type\Exception\TypeNotSupportedException;
use spaceonfire\Type\TypeInterface;
use spaceonfire\Type\VoidType;

final class VoidTypeFactory implements TypeFactoryInterface
{
    use TypeFactoryTrait;

    public function supports(string $type): bool
    {
        return VoidType::NAME === $this->removeWhitespaces($type);
    }

    public function make(string $type): TypeInterface
    {
        $type = $this->removeWhitespaces($type);

        if (!$this->supports($type)) {
            throw new TypeNotSupportedException($type, VoidType::class);
        }

        return VoidType::new();
    }
}
