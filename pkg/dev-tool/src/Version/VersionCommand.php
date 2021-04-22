<?php

declare(strict_types=1);

namespace spaceonfire\DevTool\Version;

use Symfony\Component\Console\Command\Command;

final class VersionCommand extends Command
{
    protected static $defaultName = 'version';
}
