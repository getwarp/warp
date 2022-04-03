<?php

declare(strict_types=1);

namespace Warp\Common\Kernel;

use PHPUnit\Framework\TestCase;
use Warp\Container\RawValueHolder;
use Warp\Container\ServiceProvider\AbstractServiceProvider;

class KernelTest extends TestCase
{
    private function factory(bool $debugModeEnabled = false): AbstractKernel
    {
        return new class($debugModeEnabled) extends AbstractKernel {
            public function __construct(bool $debugModeEnabled = false)
            {
                parent::__construct(null, $debugModeEnabled);
            }

            protected function loadServiceProviders(): iterable
            {
                return [
                    new class extends AbstractServiceProvider {
                        public function provides(): array
                        {
                            return [
                                'foo',
                            ];
                        }

                        public function register(): void
                        {
                            $this->getContainer()->add('foo', new RawValueHolder('bar'));
                        }
                    }
                ];
            }
        };
    }

    public function testIsDebugModeEnabled(): void
    {
        $nonDebugKernel = $this->factory(false);
        self::assertFalse($nonDebugKernel->isDebugModeEnabled());

        $debugKernel = $this->factory(true);
        self::assertTrue($debugKernel->isDebugModeEnabled());
    }

    public function testDependenciesRegisteredInContainer(): void
    {
        $kernel = $this->factory();

        self::assertSame($kernel, $kernel->getContainer()->get('kernel'));
        self::assertSame($kernel, $kernel->getContainer()->get(get_class($kernel)));
        self::assertFalse($kernel->getContainer()->get('kernel.debug'));
        self::assertSame('bar', $kernel->getContainer()->get('foo'));
    }
}
