<?php

declare(strict_types=1);

namespace Warp\DataSource;

final class IdenticalPropertyExtractor implements PropertyExtractorInterface
{
    public function extractValue(string $name, $value)
    {
        return $value;
    }

    public function extractName(string $name): string
    {
        return $name;
    }
}
