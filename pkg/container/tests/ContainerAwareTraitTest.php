<?php

declare(strict_types=1);

namespace Warp\Container;

use PHPUnit\Framework\TestCase;
use Warp\Container\Exception\ContainerException;
use Warp\Container\Fixtures\ArrayContainer;

class ContainerAwareTraitTest extends TestCase
{
    private function factory(): ContainerAwareInterface
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
        $container = new ArrayContainer([]);
        $object = $this->factory();
        $object->setContainer($container);
        self::assertSame($container, $object->getContainer());
    }
}
