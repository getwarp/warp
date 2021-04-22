<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Mapper;

use Psr\EventDispatcher\EventDispatcherInterface;

interface MapperPluginInterface extends EventDispatcherInterface
{
    /**
     * @template T of object
     * @param T $event
     * @return T
     */
    public function dispatch(object $event): object;
}
