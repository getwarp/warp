<?php

declare(strict_types=1);

namespace spaceonfire\DevTool\Monorepo\Composer;

use spaceonfire\DevTool\Shared\AbstractConfig;

final class MonorepoProject extends AbstractConfig
{
    public const DIR = 'dir';

    public const GIT = 'git';

    /**
     * @param array<string,mixed> $data
     * @return static
     */
    public static function fromArray(array $data): self
    {
        return new self($data);
    }

    public function getDir(): string
    {
        $dir = $this->getSection(self::DIR);

        \assert(\is_string($dir));

        return $dir;
    }

    public function getGit(): ?string
    {
        $git = $this->getSection(self::GIT);

        \assert(\is_string($git) || null === $git);

        return $git;
    }
}
