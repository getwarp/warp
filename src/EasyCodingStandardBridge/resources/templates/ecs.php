<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\ValueObject\Option;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::CACHE_DIRECTORY, __DIR__ . '/._ecs_cache');

    $parameters->set(Option::PATHS, [
        __DIR__ . '/src',
    ]);

    $parameters->set(Option::SKIP, [
        'Unused variable $_.' => null,
    ]);

    $containerConfigurator->import(__DIR__ . '/vendor/spaceonfire/easy-coding-standard-bridge/resources/config/spaceonfire.php', null, 'not_found');
    $containerConfigurator->import(__DIR__ . '/ecs-baseline.php', null, 'not_found');
};
