<?php

declare(strict_types=1);

namespace Warp\Type;

\class_alias(
    TypeInterface::class,
    __NAMESPACE__ . '\Type'
);

if (false) {
    /**
     * @deprecated Use {@see \Warp\Type\TypeInterface} instead.
     */
    interface Type extends TypeInterface
    {
    }
}
