<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Migrator\Input;

/**
 * @extends InputOption<bool>
 */
final class DryRunOption extends InputOption
{
    /**
     * @param string|string[] $shortcut
     */
    public function __construct(
        string $name = 'dry-run',
        $shortcut = null,
        string $description = 'Show schema changes only without running operation'
    ) {
        parent::__construct($name, $shortcut, self::VALUE_NONE, $description);
    }
}
