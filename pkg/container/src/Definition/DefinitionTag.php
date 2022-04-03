<?php

declare(strict_types=1);

namespace Warp\Container\Definition;

/**
 * List of common definitions tags. Use these constants to prevent collision that can be caused by typos in tag names.
 * Feel free to suggest tag names of your choice.
 */
final class DefinitionTag
{
    /**
     * Mark symfony/console commands
     * @var string
     * @see https://symfony.com/doc/current/reference/dic_tags.html#console-command
     */
    public const CONSOLE_COMMAND = 'console.command';
}
