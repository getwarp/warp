<?php

declare(strict_types=1);

\spl_autoload_register(static function (string $class): void {
    if (0 !== \strncmp($class, 'spaceonfire\\Type\\', 17)) {
        return;
    }

    $target = 'Warp\\Type\\' . \substr($class, 17);

    if (\class_exists($target) || \interface_exists($target) || \trait_exists($target)) {
        \class_alias($target, $class);
    }
});
