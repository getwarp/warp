<?php

declare(strict_types=1);

namespace spaceonfire\Container;

use spaceonfire\Container\Exception\ContainerException;

class ContainerAwareTraitTest extends AbstractTestCase
{
    private function factory()
    {
        return new class implements ContainerAwareInterface {
            use ContainerAwareTrait;
        };
    }

    public function testGetContainer(): void
    {
        $this->expectException(ContainerException::class);
        $this->factory()->getContainer();
    }

    public function testSetContainer(): void
    {
        $container = $this->prophesize(ContainerInterface::class)->reveal();
        $object = $this->factory();
        $object->setContainer($container);
        self::assertSame($container, $object->getContainer());
    }
}
