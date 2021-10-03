<?php

declare(strict_types=1);

namespace spaceonfire\Type\Cast;

use spaceonfire\Type\BuiltinType;
use spaceonfire\Type\TypeInterface;

final class NullCasterFactory implements CasterFactoryInterface
{
    /**
     * @phpstan-var NullCaster::ACCEPT_*
     */
    private string $accept;

    /**
     * @phpstan-param NullCaster::ACCEPT_* $accept
     */
    public function __construct(string $accept = NullCaster::ACCEPT_EMPTY)
    {
        $this->accept = $accept;
    }

    public function make(TypeInterface $type): ?CasterInterface
    {
        if (BuiltinType::null() === $type) {
            return new NullCaster($this->accept);
        }

        return null;
    }
}
