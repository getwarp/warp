<?php

declare(strict_types=1);

namespace Warp\DevTool\DI;

use Warp\Container\Factory\DefinitionTag;
use Warp\Container\ServiceProvider\AbstractServiceProvider;
use Warp\DevTool\ChangeLog\ChangeLogCommand;
use Warp\DevTool\Monorepo\Composer\MonorepoComposerCommand;
use Warp\DevTool\Monorepo\Project\ProjectsListCommand;
use Warp\DevTool\Monorepo\Project\ProjectsSplitCommand;
use Warp\DevTool\Monorepo\TestCommand;
use Warp\DevTool\Refactor\MoveClass\MoveClassCommand;
use Warp\DevTool\Version\VersionCommand;

final class CommandsProvider extends AbstractServiceProvider
{
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            DefinitionTag::CONSOLE_COMMAND,
            MoveClassCommand::class,
            ChangeLogCommand::class,
            MonorepoComposerCommand::class,
            ProjectsSplitCommand::class,
            VersionCommand::class,
            TestCommand::class,
            ProjectsListCommand::class,
        ];
    }

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->getContainer()->define(MoveClassCommand::class)->addTag(DefinitionTag::CONSOLE_COMMAND);
        $this->getContainer()->define(ChangeLogCommand::class)->addTag(DefinitionTag::CONSOLE_COMMAND);
        $this->getContainer()->define(MonorepoComposerCommand::class)->addTag(DefinitionTag::CONSOLE_COMMAND);
        $this->getContainer()->define(ProjectsSplitCommand::class)->addTag(DefinitionTag::CONSOLE_COMMAND);
        $this->getContainer()->define(VersionCommand::class)->addTag(DefinitionTag::CONSOLE_COMMAND);
        $this->getContainer()->define(TestCommand::class)->addTag(DefinitionTag::CONSOLE_COMMAND);
        $this->getContainer()->define(ProjectsListCommand::class)->addTag(DefinitionTag::CONSOLE_COMMAND);
    }
}
