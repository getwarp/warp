<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\ClassNotation\VisibilityRequiredFixer;
use PhpCsFixer\Fixer\FunctionNotation\VoidReturnFixer;
use PhpCsFixer\Fixer\Strict\StrictComparisonFixer;
use SlevomatCodingStandard\Sniffs\Functions\UnusedParameterSniff;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\ValueObject\Option;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::CACHE_DIRECTORY, __DIR__ . '/._ecs_cache');

    $parameters->set(Option::PATHS, array_merge([
        __DIR__ . '/bin',
    ], glob(__DIR__ . '/src/*/src')));

    $parameters->set(Option::SKIP, [
        'Unused variable $_.' => null,
        'Unused parameter $_.' => null,

        StrictComparisonFixer::class => [
            __DIR__ . '/src/Collection/src/Collection.php',
            __DIR__ . '/src/LaminasHydratorBridge/src/Strategy/BooleanStrategy.php',
        ],
        VoidReturnFixer::class => [
            __DIR__ . '/src/Collection/src/Collection.php',
            __DIR__ . '/src/Collection/src/TypedCollection.php',
            __DIR__ . '/src/Collection/src/ArrayHelper.php',
        ],
        UnusedParameterSniff::class => [
            __DIR__ . '/src/Collection/src/IndexedCollection.php',
            __DIR__ . '/src/Criteria/src/AbstractCriteriaDecorator.php',
            __DIR__ . '/src/LaminasHydratorBridge/src/Strategy/*',
            __DIR__ . '/src/LaminasHydratorBridge/src/NamingStrategy/AliasNamingStrategy.php',
        ],
        'Unused parameter $commandClassName.' => [
            __DIR__ . '/src/CommandBus/src/Mapping/Method/StaticMethodNameMapping.php',
        ],
        'Unused parameter $message.' => [
            __DIR__ . '/src/CommandBus/src/Bridge/PsrLog/LoggerMiddleware.php',
            __DIR__ . '/src/CommandBus/src/Bridge/SymfonyStopwatch/ProfilerMiddleware.php',
        ],
        'Class LoggerMiddleware contains unused private method compareLogLevel().' => [
            __DIR__ . '/src/CommandBus/src/Bridge/PsrLog/LoggerMiddleware.php',
        ],
        VisibilityRequiredFixer::class => [
            __DIR__ . '/src/Common/src/Kernel/ConsoleApplicationConfiguratorTrait.php',
        ],
        'SlevomatCodingStandard\Sniffs\Whitespaces\DuplicateSpacesSniff.DuplicateSpaces' => [
            __DIR__ . '/src/Collection/src/ArrayHelper.php',
        ],
    ]);

    $containerConfigurator->import(__DIR__ . '/vendor/spaceonfire/easy-coding-standard-bridge/resources/config/spaceonfire.php', null, 'not_found');
    $containerConfigurator->import(__DIR__ . '/src/EasyCodingStandardBridge/resources/config/spaceonfire.php', null, 'not_found');
    $containerConfigurator->import(__DIR__ . '/ecs-baseline.php', null, 'not_found');
};
