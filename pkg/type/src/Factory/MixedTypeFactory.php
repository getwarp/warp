<?php

declare(strict_types=1);

namespace Warp\Type\Factory;

use Warp\Type\Exception\TypeNotSupportedException;
use Warp\Type\MixedType;
use Warp\Type\TypeInterface;

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
