<?php

declare(strict_types=1);

namespace spaceonfire\Type\Factory;

use spaceonfire\Type\Exception\TypeNotSupportedException;
use spaceonfire\Type\MixedType;
use spaceonfire\Type\TypeInterface;

final class MixedTypeFactory implements TypeFactoryInterface
{
    use TypeFactoryTrait;

    /**
     * @inheritDoc
     */
    public function supports(string $type): bool
    {
        $type = $this->removeWhitespaces($type);
        return MixedType::NAME === $type;
    }

    /**
     * @inheritDoc
     */
    public function make(string $type): TypeInterface
    {
        $type = $this->removeWhitespaces($type);
        if (!$this->supports($type)) {
            throw new TypeNotSupportedException($type, MixedType::class);
        }

        return new MixedType();
    }
}
