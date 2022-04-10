<?php

declare(strict_types=1);

namespace Warp\DevTool\Monorepo\Composer;

use Warp\DevTool\Shared\AbstractConfig;

/**
 * @link https://getcomposer.org/doc/04-schema.md
 */
final class ComposerJson extends AbstractConfig
{
    public const NAME = 'name';

    public const DESCRIPTION = 'description';

    public const VERSION = 'version';

    public const TYPE = 'type';

    public const KEYWORDS = 'keywords';

    public const HOMEPAGE = 'homepage';

    public const README = 'readme';

    public const TIME = 'time';

    public const LICENSE = 'license';

    public const AUTHORS = 'authors';

    public const REPOSITORIES = 'repositories';

    public const REQUIRE = 'require';

    public const REQUIRE_DEV = 'require-dev';

    public const PROVIDE = 'provide';

    public const SUGGEST = 'suggest';

    public const REPLACE = 'replace';

    public const CONFLICT = 'conflict';

    public const BIN = 'bin';

    public const AUTOLOAD = 'autoload';

    public const AUTOLOAD_DEV = 'autoload-dev';

    public const SCRIPTS = 'scripts';

    public const SCRIPTS_DESCRIPTIONS = 'scripts-descriptions';

    public const EXTRA = 'extra';

    public const MINIMUM_STABILITY = 'minimum-stability';

    public const PREFER_STABLE = 'prefer-stable';

    public const CONFIG = 'config';

    private string $filename;

    protected function __construct(array $source, string $filename)
    {
        parent::__construct($source);

        $this->filename = $filename;
    }

    public static function read(string $filename): self
    {
        $content = \file_get_contents($filename);

        if (!$content) {
            throw new \InvalidArgumentException(\sprintf('Cannot read file: %s', $filename));
        }

        try {
            $source = \json_decode($content, true, 512, \JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new \InvalidArgumentException(\sprintf('Cannot parse JSON from file: %s', $filename));
        }

        return new self($source, $filename);
    }

    public function getName(): string
    {
        $name = $this->getSection(self::NAME);

        \assert(\is_string($name));

        return $name;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }
}
