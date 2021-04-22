<?php

declare(strict_types=1);

namespace spaceonfire\DataSource;

interface PropertyExtractorInterface
{
    /**
     * @param string $name
     * @param mixed $value
     * @return mixed
     */
    public function extractValue(string $name, $value);

    public function extractName(string $name): string;
}
