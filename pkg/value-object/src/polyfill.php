<?php

declare(strict_types=1);

\spl_autoload_register(static function (string $class): void {
    if (0 !== \strncmp($class, 'spaceonfire\\ValueObject\\', 24)) {
        return;
    }

    $target = 'Warp\\ValueObject\\' . \substr($class, 24);

    if (\class_exists($target) || \interface_exists($target) || \trait_exists($target)) {
        \class_alias($target, $class);
    }
});
