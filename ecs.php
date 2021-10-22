<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\ClassNotation\VisibilityRequiredFixer;
use PhpCsFixer\Fixer\Strict\StrictComparisonFixer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\ValueObject\Option;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::PARALLEL, true);
    $parameters->set(Option::CACHE_DIRECTORY, __DIR__ . '/._ecs_cache');

    $parameters->set(Option::PATHS, \array_merge(
        [
            __DIR__ . '/bin',
        ],
        \glob(__DIR__ . '/pkg/*/src'),
        \glob(__DIR__ . '/pkg/*/bin'),
    ));

    $parameters->set(Option::SKIP, [
        'Unused variable $_.' => null,
        'Unused parameter $_.' => null,

        StrictComparisonFixer::class => [
            __DIR__ . '/pkg/laminas-hydrator-bridge/src/Strategy/BooleanStrategy.php',
            __DIR__ . '/pkg/cycle-bridge/src/Collection/Relation/NodeHelper.php',
        ],
        'Class LoggerMiddleware contains unused private method compareLogLevel().' => [
            __DIR__ . '/pkg/command-bus/src/Middleware/Logger/LoggerMiddleware.php',
        ],
        VisibilityRequiredFixer::class => [
            __DIR__ . '/pkg/common/src/Kernel/ConsoleApplicationConfiguratorTrait.php',
        ],
    ]);

    $containerConfigurator->import(__DIR__ . '/vendor/spaceonfire/easy-coding-standard-bridge/resources/config/spaceonfire.php', null, 'not_found');
    $containerConfigurator->import(__DIR__ . '/pkg/easy-coding-standard-bridge/resources/config/spaceonfire.php', null, 'not_found');
    $containerConfigurator->import(__DIR__ . '/ecs-baseline.php', null, 'not_found');
};
