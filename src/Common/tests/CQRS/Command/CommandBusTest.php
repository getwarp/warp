<?php

declare(strict_types=1);

namespace spaceonfire\Common\CQRS\Command;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use spaceonfire\Common\_Fixtures\CQRS\Command\FixtureCommand;
use spaceonfire\Common\_Fixtures\CQRS\Command\FixtureCommandBus;
use spaceonfire\Common\_Fixtures\CQRS\Command\FixtureCommandHandler;

class CommandBusTest extends TestCase
{
    public function testDispatch(): void
    {
        $commandHandler = new FixtureCommandHandler();

        $containerProphecy = $this->prophesize(ContainerInterface::class);
        $containerProphecy->has(FixtureCommandHandler::class)->willReturn(true);
        $containerProphecy->get(FixtureCommandHandler::class)->willReturn($commandHandler);

        $commandBus = new FixtureCommandBus($containerProphecy->reveal());

        $command = new FixtureCommand();
        $commandBus->dispatch($command);

        self::assertTrue($commandHandler->isHandled($command));
    }
}
