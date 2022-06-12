<?php

declare(strict_types=1);

namespace spaceonfire\Common\Connection {
    /**
     * @deprecated Use {@see \Warp\Common\Connection\buildDsn()}
     * @param array<string,string> $options DSN elements
     * @param string $driver DSN driver prefix (`mysql` by default).
     * @return string
     */
    function buildDsn(array $options, string $driver = 'mysql'): string
    {
        return \Warp\Common\Connection\buildDsn($options, $driver);
    }
}

namespace spaceonfire\Common\Env {
    /**
     * @deprecated Use {@see \Warp\Common\Env\env()}
     * @param mixed|callable $default default value as scalar or anonymous function that returns scalar (optional)
     * @return string|bool|mixed|null
     */
    function env(string $name, $default = null)
    {
        return \Warp\Common\Env\env($name, $default);
    }
}

namespace {
    \spl_autoload_register(static function (string $class): void {
        if (0 !== \strncmp($class, 'spaceonfire\\Common\\', 19)) {
            return;
        }

        $target = 'Warp\\Common\\' . \substr($class, 19);

        if (\class_exists($target) || \interface_exists($target) || \trait_exists($target)) {
            \class_alias($target, $class);
        }
    });
}
