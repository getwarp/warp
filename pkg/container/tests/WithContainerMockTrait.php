<?php

declare(strict_types=1);

namespace Warp\Container;

use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Warp\Container\Exception\NotFoundException;

trait WithContainerMockTrait
{
    /**
     * @param array<string,mixed> $definitions
     * @param string|string[] $interface
     * @return ObjectProphecy
     */
    protected function createContainerMock(array $definitions = [], $interface = ContainerInterface::class): ObjectProphecy
    {
        $isWarpContainer = $interface === ContainerInterface::class || is_subclass_of($interface, ContainerInterface::class);

        $otherInterfaces = [];
        if (is_array($interface)) {
            $otherInterfaces = $interface;
            $interface = array_shift($otherInterfaces);
        }

        $prophecy = $this->prophesize($interface);

        foreach ($otherInterfaces as $otherInterface) {
            $prophecy->willImplement($otherInterface);
        }

        $prophecy->has(Argument::type('string'))->willReturn(false);

        $prophecy->get(Argument::type('string'))->willThrow(new NotFoundException());
        if ($isWarpContainer) {
            $prophecy->get(Argument::type('string'), Argument::type('array'))->willThrow(new NotFoundException());
        }

        foreach ($definitions as $id => $result) {
            $prophecy->has($id)->willReturn(true);

            $prophecy->get($id)->willReturn($result);
            if ($interface === ContainerInterface::class || is_subclass_of($interface, ContainerInterface::class)) {
                $prophecy->get($id, Argument::type('array'))->willReturn($result);
            }
        }

        return $prophecy;
    }
}
