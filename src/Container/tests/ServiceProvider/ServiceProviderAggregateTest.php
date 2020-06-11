<?php

declare(strict_types=1);

namespace spaceonfire\Container\ServiceProvider;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use spaceonfire\Container\ContainerInterface;
use spaceonfire\Container\Definition\Definition;
use spaceonfire\Container\Exception\ContainerException;

class ServiceProviderAggregateTest extends TestCase
{
    private function createAggregate(?ContainerInterface $container = null): ServiceProviderAggregate
    {
        if ($container === null) {
            $containerProphecy = $this->prophesize(ContainerInterface::class);
            $containerProphecy->has(Argument::type('string'))->willReturn(false);
            $containerProphecy->add(Argument::type('string'), Argument::any(), Argument::type('bool'))
                ->will(function ($args) {
                    $this->has($args[0])->willReturn(true);
                    return new Definition($args[0], $args[1], $args[2]);
                });

            $container = $containerProphecy->reveal();
        }

        $aggregate = new ServiceProviderAggregate();
        $aggregate->setContainer($container);

        return $aggregate;
    }

    private function createServiceProvider(?string $id = null, array $provides = []): ServiceProviderInterface
    {
        return new class($id, $provides) extends AbstractServiceProvider {
            private $provides;

            public function __construct(?string $id = null, array $provides = [])
            {
                if ($id) {
                    $this->setIdentifier($id);
                }

                $this->provides = $provides;
            }

            public function provides(): array
            {
                return array_keys($this->provides);
            }

            public function register(): void
            {
                foreach ($this->provides as $abstract => $concrete) {
                    $this->getContainer()->add($abstract, $concrete, true);
                }
            }
        };
    }

    public function testAddProvider(): void
    {
        $aggregate = $this->createAggregate();

        $providerA = $this->createServiceProvider();
        $providerAClone = clone $providerA;

        $providerB = clone $providerA;
        $providerB->setIdentifier('providerB');

        $aggregate->addProvider($providerA);
        $aggregate->addProvider($providerAClone);
        $aggregate->addProvider($providerB);

        self::assertCount(2, $aggregate);
        self::assertTrue($aggregate->offsetExists($providerA->getIdentifier()));
        self::assertTrue($aggregate->offsetExists($providerB->getIdentifier()));
    }

    public function testOffsetSetShouldThrowExceptionOnDuplicate(): void
    {
        $this->expectException(ContainerException::class);

        $aggregate = $this->createAggregate();
        $provider = $this->createServiceProvider('foo');

        $aggregate[] = $provider;
        $aggregate[] = $provider;
    }

    public function testProvides(): void
    {
        $aggregate = $this->createAggregate();
        $provider = $this->createServiceProvider(null, array_flip(['foo', 'bar']));
        $aggregate->addProvider($provider);

        self::assertTrue($aggregate->provides('foo'));
        self::assertTrue($aggregate->provides('bar'));
        self::assertFalse($aggregate->provides('baz'));
    }

    public function testBootableServiceProvider(): void
    {
        $aggregate = $this->createAggregate();

        $provider = new class extends AbstractServiceProvider implements BootableServiceProviderInterface {
            public $booted = false;

            public function boot(): void
            {
                $this->booted = true;
            }

            public function provides(): array
            {
                return [];
            }

            public function register(): void
            {
            }
        };

        $aggregate->addProvider($provider);

        self::assertTrue($provider->booted);
    }

    public function testRegister(): void
    {
        $aggregate = $this->createAggregate();
        $provider = $this->createServiceProvider(null, array_flip(['foo', 'bar']));
        $aggregate->addProvider($provider);
        $aggregate->register('foo');

        self::assertTrue($aggregate->getContainer()->has('foo'));

        // Second register call should early exit
        $aggregate->register('foo');
    }

    public function testRegisterFailed(): void
    {
        $this->expectException(ContainerException::class);
        $aggregate = $this->createAggregate();
        $aggregate->register('foo');
    }
}
