<?php

declare(strict_types=1);

namespace Warp\Container\ServiceProvider;

use Prophecy\Argument;
use Warp\Container\AbstractTestCase;
use Warp\Container\ContainerInterface;
use Warp\Container\Definition\Definition;
use Warp\Container\Exception\ContainerException;

class ServiceProviderAggregateTest extends AbstractTestCase
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

    private function createServiceProvider(?string $id = null, array $provides = [], array $tags = []): ServiceProviderInterface
    {
        return new class($id, $provides, $tags) extends AbstractServiceProvider {
            private $provides;
            private $tags;
            private $registered = false;

            public function __construct(?string $id = null, array $provides = [], array $tags = [])
            {
                if ($id) {
                    $this->setIdentifier($id);
                }

                $this->provides = $provides;
                $this->tags = $tags;
            }

            public function provides(): array
            {
                return array_merge(array_keys($this->provides), $this->tags);
            }

            public function register(): void
            {
                foreach ($this->provides as $abstract => $concrete) {
                    $def = $this->getContainer()->add($abstract, $concrete, true);

                    foreach ($this->tags as $tag) {
                        $def->addTag($tag);
                    }
                }
                $this->registered = true;
            }

            public function isRegistered(): bool
            {
                return $this->registered;
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

    public function testRegisterMultipleProviders(): void
    {
        $aggregate = $this->createAggregate();

        $fooProvider = $this->createServiceProvider('foo', ['foo' => 'foo'], ['tag']);
        $barProvider = $this->createServiceProvider('bar', ['bar' => 'bar'], ['tag']);

        $aggregate->addProvider($fooProvider);
        $aggregate->addProvider($barProvider);

        $aggregate->register('tag');

        self::assertTrue($fooProvider->isRegistered());
        self::assertTrue($barProvider->isRegistered());
    }
}
