#!/usr/bin/env php
<?php

declare(strict_types=1);

try {
    $cwd = getcwd();

    $copyFiles = [
        __DIR__ . '/../resources/templates/.editorconfig',
        __DIR__ . '/../resources/templates/ecs.php',
    ];

    foreach ($copyFiles as $sourceFile) {
        $filename = basename($sourceFile);
        $targetFile = $cwd . '/' . $filename;

        if (
            file_exists($targetFile)
            && false === ask_for_confirm(
                sprintf('File "%s" exist in working directory. Would you like to overwrite it?', $filename),
                false
            )
        ) {
            echo sprintf('Make sure to configure "%s" according to coding standard.' . PHP_EOL, $filename);
            continue;
        }

        if (false === copy($sourceFile, $targetFile)) {
            throw new RuntimeException(sprintf('Cannot copy file "%s"', $filename));
        }
        echo sprintf('File "%s" copied to working directory.' . PHP_EOL, $filename);
    }

    echo sprintf(
        'Are you working on a legacy project? Make sure to use `%s`.' . PHP_EOL,
        dirname($_SERVER['SCRIPT_NAME']) . '/ecs-baseliner.php'
    );
} catch (Throwable $e) {
    fwrite(STDERR, $e->getMessage() . PHP_EOL);
    exit(1);
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
