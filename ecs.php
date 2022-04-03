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

    $parameters->set(Option::PATHS, glob(__DIR__ . '/pkg/*/src'));

    $parameters->set(Option::SKIP, [
        'Unused variable $_.' => null,
        'Unused parameter $_.' => null,

        StrictComparisonFixer::class => [
            __DIR__ . '/pkg/collection/src/Collection.php',
            __DIR__ . '/pkg/laminas-hydrator-bridge/src/Strategy/BooleanStrategy.php',
        ],
        VoidReturnFixer::class => [
            __DIR__ . '/pkg/collection/src/Collection.php',
            __DIR__ . '/pkg/collection/src/TypedCollection.php',
            __DIR__ . '/pkg/collection/src/ArrayHelper.php',
        ],
        UnusedParameterSniff::class => [
            __DIR__ . '/pkg/collection/src/IndexedCollection.php',
            __DIR__ . '/pkg/criteria/src/AbstractCriteriaDecorator.php',
            __DIR__ . '/pkg/laminas-hydrator-bridge/src/Strategy/*',
            __DIR__ . '/pkg/laminas-hydrator-bridge/src/NamingStrategy/AliasNamingStrategy.php',
        ],
        'Unused parameter $commandClassName.' => [
            __DIR__ . '/pkg/command-bus/src/Mapping/Method/StaticMethodNameMapping.php',
        ],
        'Unused parameter $message.' => [
            __DIR__ . '/pkg/command-bus/src/Bridge/PsrLog/LoggerMiddleware.php',
            __DIR__ . '/pkg/command-bus/src/Bridge/SymfonyStopwatch/ProfilerMiddleware.php',
        ],
        'Class LoggerMiddleware contains unused private method compareLogLevel().' => [
            __DIR__ . '/pkg/command-bus/src/Bridge/PsrLog/LoggerMiddleware.php',
        ],
        VisibilityRequiredFixer::class => [
            __DIR__ . '/pkg/common/src/Kernel/ConsoleApplicationConfiguratorTrait.php',
        ],
        'SlevomatCodingStandard\Sniffs\Whitespaces\DuplicateSpacesSniff.DuplicateSpaces' => [
            __DIR__ . '/pkg/collection/src/ArrayHelper.php',
        ],
    ]);

    $containerConfigurator->import(__DIR__ . '/vendor/getwarp/easy-coding-standard-bridge/resources/config/warp.php', null, 'not_found');
    $containerConfigurator->import(__DIR__ . '/pkg/easy-coding-standard-bridge/resources/config/warp.php', null, 'not_found');
    $containerConfigurator->import(__DIR__ . '/ecs-baseline.php', null, 'not_found');
};
