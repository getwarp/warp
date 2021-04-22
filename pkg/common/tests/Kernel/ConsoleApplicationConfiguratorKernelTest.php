<?php

declare(strict_types=1);

namespace spaceonfire\Common\Kernel;

use PHPUnit\Framework\TestCase;
use spaceonfire\Container\Factory\DefinitionTag;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;

class ConsoleApplicationConfiguratorKernelTest extends TestCase
{
    /**
     * @param bool $debugModeEnabled
     * @return AbstractKernel|ConsoleApplicationConfiguratorTrait
     */
    private function factory(bool $debugModeEnabled = false): AbstractKernel
    {
        return new class($debugModeEnabled) extends AbstractKernel {
            use ConsoleApplicationConfiguratorTrait;

            public function __construct(bool $debugModeEnabled = false)
            {
                parent::__construct(null, $debugModeEnabled);
            }
        };
    }

    public function testConfigureConsoleApplication(): void
    {
        $kernel = $this->factory();

        $command = new Command('foo');

        $kernel->getContainer()->define('foo', $command)->addTag(DefinitionTag::CONSOLE_COMMAND);

        $application = new Application();
        $input = new ArrayInput([]);
        $output = new NullOutput();

        $kernel->configureConsoleApplication($application, $input, $output);

        self::assertSame($command, $application->get('foo'));
        self::assertSame($input, $kernel->getContainer()->get(InputInterface::class));
        self::assertSame($output, $kernel->getContainer()->get(OutputInterface::class));
    }

    public function testDetermineDebugModeFromConsoleInputUsingDebugOption(): void
    {
        $kernel = $this->factory();

        self::assertFalse($kernel->isDebugModeEnabled());

        $application = new Application();
        $input = new ArrayInput([
            '--debug' => true,
        ]);
        $output = new NullOutput();

        $kernel->configureConsoleApplication($application, $input, $output);

        self::assertTrue($kernel->isDebugModeEnabled());
    }

    public function testDetermineDebugModeFromConsoleInputUsingVerbosityOption(): void
    {
        $kernel = $this->factory();

        self::assertFalse($kernel->isDebugModeEnabled());

        $application = new Application();
        $input = new ArrayInput([
            '-vvv' => true,
        ]);
        $output = new NullOutput();

        $kernel->configureConsoleApplication($application, $input, $output);

        self::assertTrue($kernel->isDebugModeEnabled());
    }
}
