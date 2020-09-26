<?php

declare(strict_types=1);

use SlevomatCodingStandard\Sniffs\Functions\UnusedInheritedVariablePassedToClosureSniff;
use SlevomatCodingStandard\Sniffs\Functions\UnusedParameterSniff;
use SlevomatCodingStandard\Sniffs\Variables\UnusedVariableSniff;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set('sets', [
        SetList::ARRAY,
        SetList::CLEAN_CODE,
        SetList::STRICT,
        SetList::PHP_70,
        SetList::PHP_71,
        SetList::PSR_12,
    ]);

    $parameters->set('cache_directory', __DIR__ . '/._ecs_cache');

    $parameters->set('paths', [
        __DIR__ . '/src',
    ]);

    $parameters->set('skip', [
        'Unused variable $_.' => null,
    ]);

    $services = $containerConfigurator->services();

    $services->set(LineLengthFixer::class);
    $services->set(UnusedInheritedVariablePassedToClosureSniff::class);
    $services->set(UnusedParameterSniff::class);
    $services->set(UnusedVariableSniff::class);
};
