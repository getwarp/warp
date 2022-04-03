<?php

declare(strict_types=1);

namespace Warp\Common\Env;

use Dotenv\Dotenv;
use Dotenv\Repository\RepositoryBuilder;

/**
 * Returns an environment variable.
 *
 * @param string $name env name
 * @param mixed|callable $default default value as scalar or anonymous function that returns scalar (optional)
 * @return string|bool|mixed|null
 */
function env(string $name, $default = null)
{
    static $dotEnv;

    if (null === $dotEnv) {
        $envFileName = defined('SOF_ENV_FILE_NAME') ? SOF_ENV_FILE_NAME : null;

        /** @var string[] $envPath */
        $envPath = defined('SOF_ENV_PATH') ? SOF_ENV_PATH : [];

        if (!is_array($envPath)) {
            $envPath = [$envPath];
        }

        // @codeCoverageIgnoreStart
        if (str_ends_with(__DIR__, 'vendor/getwarp/common/src/Env')) {
            $envPath[] = dirname(__DIR__, 5);
        }

        if (isset($_SERVER['DOCUMENT_ROOT'])) {
            $envPath[] = rtrim($_SERVER['DOCUMENT_ROOT'], '\\/');
        }

        if (\PHP_SAPI === 'cli') {
            $envPath[] = getcwd() ?: '';
        }
        // @codeCoverageIgnoreStop

        $envPath = array_filter(array_unique($envPath), static function ($path) {
            return is_dir($path);
        });

        $builder = method_exists(RepositoryBuilder::class, 'createWithDefaultAdapters')
            ? RepositoryBuilder::createWithDefaultAdapters()
            : RepositoryBuilder::create();

        $dotEnv = $builder->immutable()->make();
        Dotenv::create($dotEnv, array_unique($envPath), $envFileName)->safeLoad();
    }

    $value = $dotEnv->get($name);

    if (null === $value) {
        return is_callable($default)
            ? $default()
            : $default;
    }

    switch (strtolower($value)) {
        case 'true':
        case '(true)':
            return true;
        case 'false':
        case '(false)':
            return false;
        case 'empty':
        case '(empty)':
            return '';
        case 'null':
        case '(null)':
            return null;
    }

    if (preg_match('/\A([\'"])(.*)\1\z/', $value, $matches)) {
        return $matches[2];
    }

    return $value;
}
