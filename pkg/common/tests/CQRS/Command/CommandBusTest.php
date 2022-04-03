<?php

declare(strict_types=1);

namespace Warp\Common\CQRS\Command;

use Psr\Container\ContainerInterface;
use Warp\Common\_Fixtures\CQRS\Command\FixtureCommand;
use Warp\Common\_Fixtures\CQRS\Command\FixtureCommandBus;
use Warp\Common\_Fixtures\CQRS\Command\FixtureCommandHandler;
use Warp\Common\AbstractTestCase;

class CommandBusTest extends AbstractTestCase
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
