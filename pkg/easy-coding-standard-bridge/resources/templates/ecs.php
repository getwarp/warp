<?php

declare(strict_types=1);

use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Option;

return static function (ECSConfig $config): void {
    $parameters = $config->parameters();

    $parameters->set(Option::PARALLEL, true);
    $parameters->set(Option::CACHE_DIRECTORY, __DIR__ . '/._ecs_cache');

    $parameters->set(Option::PATHS, [
        __DIR__ . '/src',
    ]);

    $parameters->set(Option::SKIP, [
        'Unused variable $_.' => null,
    ]);

    $config->import(__DIR__ . '/vendor/getwarp/easy-coding-standard-bridge/resources/config/warp.php', null, 'not_found');
    $config->import(__DIR__ . '/ecs-baseline.php', null, 'not_found');
};
