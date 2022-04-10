<?php

declare(strict_types=1);

namespace Warp\DevTool\Version;

use Symfony\Component\Console\Command\Command;

final class VersionCommand extends Command
{
    protected static $defaultName = 'version';
}
