<?php

declare(strict_types=1);

namespace spaceonfire\Common\Kernel;

use spaceonfire\Container\ContainerInterface;
use spaceonfire\Container\Definition\DefinitionTag;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

trait ConsoleApplicationConfiguratorTrait
{
    /**
     * Configure symfony/console application
     * @param Application $app
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public function configureConsoleApplication(
        Application $app,
        InputInterface $input,
        OutputInterface $output
    ): void {
        if (\PHP_SAPI !== 'cli') {
            // @codeCoverageIgnoreStart
            trigger_error(sprintf(
                'Method %s::configureConsoleApplication() called in not CLI environment.',
                static::class
            ));
            return;
            // @codeCoverageIgnoreStop
        }

        // Register console I/O in container first. It can be used as nested dependencies of some command (logger for example)
        $this->getContainer()->share(InputInterface::class, $input);
        $this->getContainer()->share(OutputInterface::class, $output);

        $this->determineDebugModeFromConsoleInput($input);

        $commands = $this->getContainer()->getTagged(DefinitionTag::CONSOLE_COMMAND);

        foreach ($commands as $command) {
            $app->add($command);
        }
    }

    /**
     * @noinspection AccessModifierPresentedInspection
     */
    abstract function getContainer(): ContainerInterface;

    /**
     * @noinspection AccessModifierPresentedInspection
     */
    abstract function enableDebugMode(bool $debug);

    protected function determineDebugModeFromConsoleInput(InputInterface $input): void
    {
        // Debug option defined and bool(true) value
        if ($input->hasParameterOption('--debug', true)) {
            $this->enableDebugMode(true);
            return;
        }

        // Verbosity level set to 3+
        if ($input->hasParameterOption('-vvv', true)) {
            $this->enableDebugMode(true);
            return;
        }
    }
}
