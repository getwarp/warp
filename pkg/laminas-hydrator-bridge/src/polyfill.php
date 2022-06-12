<?php

declare(strict_types=1);

\spl_autoload_register(static function (string $class): void {
    if (0 !== \strncmp($class, 'spaceonfire\\LaminasHydratorBridge\\', 34)) {
        return;
    }

    $target = 'Warp\\LaminasHydratorBridge\\' . \substr($class, 34);

    if (\class_exists($target) || \interface_exists($target) || \trait_exists($target)) {
        \class_alias($target, $class);
    }
});
