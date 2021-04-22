<?php

declare(strict_types=1);

namespace spaceonfire\Type\Factory;

use spaceonfire\Type\Exception\TypeNotSupportedException;
use spaceonfire\Type\MixedType;
use spaceonfire\Type\TypeInterface;

final class MixedTypeFactory implements TypeFactoryInterface
{
    use TypeFactoryTrait;

    public function supports(string $type): bool
    {
        return MixedType::NAME === $this->removeWhitespaces($type);
    }

    public function make(string $type): TypeInterface
    {
        $type = $this->removeWhitespaces($type);

        if (!$this->supports($type)) {
            throw new TypeNotSupportedException($type, MixedType::class);
        }

        return MixedType::new();
    }
}
