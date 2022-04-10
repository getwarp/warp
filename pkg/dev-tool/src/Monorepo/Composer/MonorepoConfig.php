<?php

declare(strict_types=1);

namespace Warp\DevTool\Monorepo\Composer;

use Warp\DevTool\Shared\AbstractConfig;

final class MonorepoConfig extends AbstractConfig
{
    public const PROJECTS = 'projects';

    public const REQUIRE = 'require';

    public const REQUIRE_DEV = 'require-dev';

    private string $filename;

    protected function __construct(array $source, string $filename)
    {
        parent::__construct($source);

        $this->filename = $filename;
    }

    public static function fromComposer(ComposerJson $composerJson): self
    {
        /** @var array<string,mixed> $extra */
        $extra = $composerJson->getSection(ComposerJson::EXTRA, []);

        return new self($extra['monorepo'] ?? [], $composerJson->getFilename());
    }

    /**
     * @return MonorepoProject[]
     */
    public function getProjects(): iterable
    {
        $projects = $this->getSection(self::PROJECTS, []);

        \assert(\is_array($projects));

        return \array_map(static fn ($project) => MonorepoProject::fromArray($project), $projects);
    }

    public function getFilename(): string
    {
        return $this->filename;
    }
}
