<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection;
use Symplify\MonorepoBuilder\ValueObject\Option;

$defaultComposerJson = [
    ComposerJsonSection::NAME => 'spaceonfire/spaceonfire',
    ComposerJsonSection::TYPE => 'project',
    ComposerJsonSection::DESCRIPTION => '',
    ComposerJsonSection::KEYWORDS => [],
    ComposerJsonSection::HOMEPAGE => 'https://github.com/spaceonfire/spaceonfire',
    ComposerJsonSection::LICENSE => 'MIT',
    ComposerJsonSection::AUTHORS => [
        [
            'name' => 'Constantine Karnaukhov',
            'email' => 'genteelknight@gmail.com',
            'homepage' => 'https://www.onfire.space',
            'role' => 'Maintainer',
        ],
    ],
    ComposerJsonSection::REQUIRE => [
        'php' => '^7.2|^8.0',
    ],
    ComposerJsonSection::REQUIRE_DEV => [
        'roave/security-advisories' => 'dev-latest',
        'phpunit/phpunit' => '^8.5.20|^9.5',
        'phpspec/prophecy' => '^1.13',
        'phpspec/prophecy-phpunit' => '^1.1|^2.0',
        'phpstan/phpstan' => '^0.12.84',
        'phpstan/phpstan-webmozart-assert' => '^0.12.12',
        'symplify/easy-coding-standard' => '^9.2',
        'symplify/monorepo-builder' => '^8.3|^9.2',
        'slevomat/coding-standard' => '^7.0',
    ],
    ComposerJsonSection::SCRIPTS => [
        'test' => '@php -d xdebug.mode=coverage `which phpunit`',
        'codestyle' => '@php -d xdebug.mode=off `which ecs` check --ansi',
        'lint' => '@php -d xdebug.mode=off `which phpstan` analyze --memory-limit=512M --ansi',
    ],
    ComposerJsonSection::EXTRA => [
        'branch-alias' => [
            'dev-master' => '2.5-dev',
        ],
    ],
    ComposerJsonSection::MINIMUM_STABILITY => 'dev',
    ComposerJsonSection::PREFER_STABLE => true,
    ComposerJsonSection::CONFIG => [
        'preferred-install' => 'dist',
        'sort-packages' => true,
    ],
];

/**
 * @see https://github.com/symplify/symplify/issues/2061
 */
\register_shutdown_function(static function () use ($defaultComposerJson): void {
    $actualComposerJson = \json_decode(\file_get_contents(__DIR__ . '/composer.json'), true);

    if (null === $actualComposerJson) {
        return;
    }

    $result = \array_merge($defaultComposerJson, $actualComposerJson);

    $json = \json_encode($result, \JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES);
    $json = \str_replace('    ', '  ', $json);

    \file_put_contents(__DIR__ . '/composer.json', $json . "\n");
});

return static function (ContainerConfigurator $containerConfigurator) use ($defaultComposerJson): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::PACKAGE_DIRECTORIES, [
        __DIR__ . '/pkg',
    ]);

    $parameters->set(Option::DATA_TO_APPEND, $defaultComposerJson);
};
