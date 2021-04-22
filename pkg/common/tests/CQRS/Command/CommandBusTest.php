<?php

declare(strict_types=1);

namespace spaceonfire\Common\CQRS\Command;

use PHPUnit\Framework\TestCase;
use spaceonfire\Common\Fixtures\CQRS\Command\FixtureCommand;
use spaceonfire\Common\Fixtures\CQRS\Command\FixtureCommandBus;
use spaceonfire\Common\Fixtures\CQRS\Command\FixtureCommandHandler;
use spaceonfire\Container\FactoryInterface;
use spaceonfire\Container\FactoryOptionsInterface;
use spaceonfire\Container\Fixtures\ArrayFactoryAggregate;

/**
 * @todo: replace ArrayFactoryAggregate
 */
class CommandBusTest extends TestCase
{
    public function testDispatch(): void
    {
        $commandHandler = new FixtureCommandHandler();

        $commandBus = new FixtureCommandBus(new ArrayFactoryAggregate([
            FixtureCommandHandler::class => new class($commandHandler) implements FactoryInterface {
                private FixtureCommandHandler $object;

                public function __construct(FixtureCommandHandler $object)
                {
                    $this->object = $object;
                }

                public function make(?FactoryOptionsInterface $options = null)
                {
                    return $this->object;
                }
            },
        ]));

        $command = new FixtureCommand();
        $commandBus->dispatch($command);

        self::assertTrue($commandHandler->isHandled($command));
    }
}
