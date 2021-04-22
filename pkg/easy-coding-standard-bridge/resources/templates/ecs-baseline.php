<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\ValueObject\Option;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $baselineErrors = [];

    $skipSettings = [];

    foreach ($baselineErrors as $filename => $errors) {
        foreach ($errors as $error) {
            if (isset($skipSettings[$error])) {
                $skipSettings[$error][] = $filename;
            } else {
                $skipSettings[$error] = [$filename];
            }
        }
    }

    $parameters->set(Option::SKIP, $skipSettings);
};
