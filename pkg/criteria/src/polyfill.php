<?php

declare(strict_types=1);

\spl_autoload_register(static function (string $class): void {
    if (0 !== \strncmp($class, 'spaceonfire\\Criteria\\', 21)) {
        return;
    }

    $target = 'Warp\\Criteria\\' . \substr($class, 21);

    if (\class_exists($target) || \interface_exists($target) || \trait_exists($target)) {
        \class_alias($target, $class);
    }
});
