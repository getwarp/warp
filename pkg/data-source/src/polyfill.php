<?php

declare(strict_types=1);

\spl_autoload_register(static function (string $class): void {
    if (0 !== \strncmp($class, 'spaceonfire\\DataSource\\', 23)) {
        return;
    }

    $target = 'Warp\\DataSource\\' . \substr($class, 23);

    if (\class_exists($target) || \interface_exists($target) || \trait_exists($target)) {
        \class_alias($target, $class);
    }
});
