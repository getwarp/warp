<?php

declare(strict_types=1);

namespace Warp\DevTool\Console;

use Symfony\Component\Console\Helper\Helper;
use Warp\DevTool\Monorepo\Composer\ComposerJson;

final class ComposerHelper extends Helper
{
    public const NAME = 'composer';

    private string $composerJsonPath;

    private ?ComposerJson $composerJson = null;

    public function __construct(string $composerJsonPath)
    {
        $this->composerJsonPath = $composerJsonPath;
    }

    public function getComposerJson(): ComposerJson
    {
        return $this->composerJson ??= ComposerJson::read($this->composerJsonPath);
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
