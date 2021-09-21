<?php

declare(strict_types=1);

namespace spaceonfire\Type;

\class_alias(
    TypeInterface::class,
    __NAMESPACE__ . '\Type'
);

if (false) {
    /**
     * @deprecated Use {@see \spaceonfire\Type\TypeInterface} instead.
     */
    interface Type extends TypeInterface
    {
    }
}
