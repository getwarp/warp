#!/usr/bin/env php
<?php

/*
 * Usage:
 * php bin/coverage-badges.php <clover> <gist-id> <gist-token>
 */

declare(strict_types=1);

try {
    $arguments = $argv;
    array_shift($arguments);

    [$clover, $gistId, $gistToken] = $arguments + [null, null, null];

    if (null === $clover) {
        throw new InvalidArgumentException('Clover filename not specified');
    }

    if (!is_file($clover)) {
        throw new InvalidArgumentException('Clover file not readable');
    }

    if (null === $gistId) {
        throw new InvalidArgumentException('Gist id not specified');
    }

    if (null === $gistToken) {
        throw new InvalidArgumentException('Gist token not specified');
    }

    $coveragePerProject = collectCoveragePerProject($clover, [
        'collection' => 'src/Collection',
        'command-bus' => 'src/CommandBus',
        'common' => 'src/Common',
        'container' => 'src/Container',
        'criteria' => 'src/Criteria',
        'data-source' => 'src/DataSource',
        'type' => 'src/Type',
        'value-object' => 'src/ValueObject',
        'laminas-hydrator-bridge' => 'src/LaminasHydratorBridge',
        'monolog-bridge' => 'src/MonologBridge',
    ]);

    submitCoverageBadges($coveragePerProject, $gistId, $gistToken);

    echo 'Coverage badge info uploaded' . PHP_EOL;
    exit(0);
} catch (Throwable $e) {
    fwrite(STDERR, $e->getMessage() . PHP_EOL);
    exit(1);
}

function collectCoveragePerProject(string $filename, array $projects, ?string $workingDir = null): \Generator
{
    $workingDir ??= getcwd() ?: '';
    $cloverXml = new \SimpleXMLElement($filename, 0, true);

    $coveragePerProject = [];

    foreach ($cloverXml->xpath('//file') as $file) {
        $name = ltrim(str_replace($workingDir, '', (string)$file['name']), '/\\');

        foreach ($projects as $projectCode => $projectDir) {
            if (!str_starts_with($name, $projectDir)) {
                continue;
            }

            if (!isset($coveragePerProject[$projectCode])) {
                $coveragePerProject[$projectCode] = [
                    'total' => 0,
                    'covered' => 0,
                ];
            }

            $coveragePerProject[$projectCode]['total'] += (int)$file->metrics['statements'];
            $coveragePerProject[$projectCode]['covered'] += (int)$file->metrics['coveredstatements'];
            break;
        }
    }

    foreach ($coveragePerProject as $projectCode => $projectLines) {
        yield $projectCode => $projectLines['covered'] / $projectLines['total'] * 100;
    }
}

function submitCoverageBadges(iterable $coveragePerProject, string $gistId, string $gistToken): void
{
    $files = [];

    foreach ($coveragePerProject as $project => $coverage) {
        $files[sprintf('%s.json', $project)] = [
            'content' => json_encode(getCoverageBadge($coverage), JSON_THROW_ON_ERROR),
        ];
    }

    $postFields = json_encode([
        'files' => $files,
    ], JSON_THROW_ON_ERROR);

    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => sprintf('https://api.github.com/gists/%s', $gistId),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'PATCH',
        CURLOPT_POSTFIELDS => $postFields,
        CURLOPT_HTTPHEADER => [
            'accept: application/vnd.github.v3+json',
            sprintf('authorization: Bearer %s', $gistToken),
            'content-type: application/json',
            'user-agent: curl',
        ],
    ]);

    $response = curl_exec($curl);
    $json = json_decode($response, true, 512, JSON_THROW_ON_ERROR);
    $err = curl_error($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);

    curl_close($curl);

    if ($err) {
        throw new RuntimeException(sprintf('Curl error: %s', $err));
    }

    if (399 < $httpCode) {
        $exceptionMessage = sprintf('Request error: %s', $json['message'] ?? $response);

        if (isset($json['errors'])) {
            $exceptionMessage .= PHP_EOL . PHP_EOL . 'Errors: ' . json_encode($json['errors'], JSON_PRETTY_PRINT);
        }

        throw new RuntimeException($exceptionMessage);
    }
}

function getCoverageBadge($coverage): array
{
    $message = sprintf(
        '%s%%',
        (string)$coverage === (string)(int)$coverage
            ? (string)(int)$coverage
            : number_format($coverage, 1)
    );

    $color = 'red';

    if (30 <= $coverage) {
        $color = 'orange';
    }
    if (50 <= $coverage) {
        $color = 'yellow';
    }
    if (65 <= $coverage) {
        $color = 'yellowgreen';
    }
    if (80 <= $coverage) {
        $color = 'green';
    }
    if (95 <= $coverage) {
        $color = 'brightgreen';
    }

    return [
        'schemaVersion' => 1,
        'label' => 'coverage',
        'message' => $message,
        'color' => $color,
    ];
}
