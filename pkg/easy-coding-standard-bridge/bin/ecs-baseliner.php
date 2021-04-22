#!/usr/bin/env php
<?php

/**
 * Usage: php bin/ecs-baseliner.php [--print] <...error_files>
 */

declare(strict_types=1);

use Symfony\Component\VarExporter\VarExporter;

try {
    include_autoloader();

    if (!class_exists(VarExporter::class)) {
        throw new RuntimeException('Install VarExporter. Run `composer require --dev symfony/var-exporter`.');
    }

    $arguments = $argv;
    array_shift($arguments);

    $isPrint = '--print' === $arguments[0];
    if ($isPrint) {
        array_shift($arguments);
    }

    $errorFiles = $arguments;

    if (0 === count($errorFiles)) {
        echo <<<HELP
Collect errors to skip first:

    vendor/bin/ecs check --output-format=json > ecs-baseline-errors.json

Then run ecs-baseliner.php with error files specified:

    vendor/bin/ecs-baseliner.php ecs-baseline-errors.json

Import ecs-baseline.php in ecs.php config (if it not already). And check that errors ignored.
If there some new errors found, collect them in separate json file (eg, ecs-baseline-errors.2.json)
and recompile ecs-baseline.php config with all error files specified:

    vendor/bin/ecs-baseliner.php ecs-baseline-errors*.json

Repeat until all baseline errors are skipped.

If you want to preview generated config, specify `--print` argument first:

    vendor/bin/ecs-baseliner.php --print ecs-baseline-errors*.json


HELP;
        exit(0);
    }

    // compile
    $errorFiles = find_error_files($errorFiles);

    if (0 === count($errorFiles)) {
        throw new InvalidArgumentException('Files not found.');
    }

    $baselineErrors = collect_baseline_errors($errorFiles);
    $baselineConfig = render_baseline_config($baselineErrors);

    if ($isPrint) {
        echo $baselineConfig;
        exit(0);
    }

    $targetFile = getcwd() . '/ecs-baseline.php';

    if (
        file_exists($targetFile)
        && false === ask_for_confirm(
            'File "ecs-baseline.php" exist in working directory. Would you like to overwrite it?',
            true
        )
    ) {
        exit(0);
    }

    file_put_contents($targetFile, $baselineConfig, LOCK_EX);
} catch (Throwable $e) {
    fwrite(STDERR, $e->getMessage() . PHP_EOL);
    exit(1);
}

function include_autoloader(): void
{
    // Local autoloader
    if (file_exists($filename = __DIR__ . '/../vendor/autoload.php')) {
        require $filename;
        return;
    }

    // Installed as dependency
    if (file_exists($filename = __DIR__ . '/../../../autoload.php')) {
        require $filename;
        return;
    }

    // Try to find in working dir
    if (file_exists($filename = getcwd() . '/vendor/autoload.php')) {
        require $filename;
        return;
    }
}

/**
 * @param string[] $input
 * @return string[]
 */
function find_error_files(array $input): array
{
    $output = [];

    foreach ($input as $i) {
        if (is_file($i)) {
            $output[] = realpath($i);
            continue;
        }

        if (is_dir($i)) {
            $i .= '/**/*.json';
        }

        foreach (glob($i) ?: [] as $file) {
            $output[] = realpath($file);
        }
    }

    return array_unique(array_filter($output));
}

/**
 * @param string[] $errorFiles
 * @return array<string,array<string>>
 */
function collect_baseline_errors(array $errorFiles): array
{
    $output = [];

    foreach ($errorFiles as $filename) {
        if (false === $content = file_get_contents($filename)) {
            throw new RuntimeException(sprintf('Unable to read file %s', $filename));
        }

        $content = json_decode($content, true, 512, JSON_THROW_ON_ERROR);

        if (!is_array($content)) {
            throw new RuntimeException(sprintf('Invalid JSON in "%s"', $filename));
        }

        /**
         * @var string $fileToSkip
         * @var array<string,mixed> $foundErrors
         */
        foreach ($content['files'] as $fileToSkip => $foundErrors) {
            if (!isset($output[$fileToSkip])) {
                $output[$fileToSkip] = [];
            }

            if (isset($foundErrors['errors'])) {
                foreach ($foundErrors['errors'] as $error) {
                    $output[$fileToSkip][] = $error['message'];
                }
            }

            if (isset($foundErrors['diffs'])) {
                foreach ($foundErrors['diffs'] as $warning) {
                    foreach ($warning['applied_checkers'] as $skippedRule) {
                        $output[$fileToSkip][] = $skippedRule;
                    }
                }
            }
        }
    }

    foreach ($output as &$skippedErrors) {
        $skippedErrors = array_unique($skippedErrors);
    }
    unset($skippedErrors);

    return $output;
}

/**
 * @param array<string,array<string>> $baselineErrors
 * @return string
 */
function render_baseline_config(array $baselineErrors): string
{
    $content = file_get_contents(__DIR__ . '/../resources/templates/ecs-baseline.php');

    if (false === $content) {
        throw new RuntimeException('Unable to read ecs-baseline.php config template.');
    }

    $baselineErrorsValue = VarExporter::export($baselineErrors);

    return str_replace(
        '$baselineErrors = [];',
        trim(indent(sprintf('$baselineErrors = %s;', $baselineErrorsValue))),
        $content
    );
}

function indent(string $code, int $level = 1): string
{
    return implode(
        PHP_EOL,
        array_map(
            static fn ($line) => str_repeat('    ', $level) . rtrim($line),
            explode(PHP_EOL, $code)
        )
    );
}

function ask_for_input(string $prompt): string
{
    $prompt = trim($prompt) . ': ';
    if (function_exists('readline')) {
        $line = trim(readline($prompt) ?: '');
        if (!empty($line)) {
            readline_add_history($line);
        }
    } else {
        echo $prompt;
        $line = trim(fgets(STDIN) ?: '');
    }
    return $line;
}

function ask_for_confirm(string $prompt, ?bool $default = null): bool
{
    $input = strtolower(ask_for_input(sprintf(
        '%s [%s/%s]',
        $prompt,
        true === $default ? 'Y' : 'y',
        false === $default ? 'N' : 'n'
    )));

    if ('' === $input && null !== $default) {
        return $default;
    }

    if ('y' === $input) {
        return true;
    }

    if ('n' === $input) {
        return false;
    }

    return ask_for_confirm($prompt, $default);
}
