<?php

declare(strict_types=1);

namespace Warp\Common\CQRS\Command;

use PHPUnit\Framework\TestCase;
use Warp\Common\Fixtures\CQRS\Command\FixtureCommand;
use Warp\Common\Fixtures\CQRS\Command\FixtureCommandBus;
use Warp\Common\Fixtures\CQRS\Command\FixtureCommandHandler;
use Warp\Container\FactoryInterface;
use Warp\Container\FactoryOptionsInterface;
use Warp\Container\Fixtures\ArrayFactoryAggregate;

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
