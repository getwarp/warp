<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Mapper\Plugin;

use Psr\EventDispatcher\EventDispatcherInterface;
use spaceonfire\Bridge\Cycle\Mapper\MapperPluginInterface;

final class DispatcherMapperPlugin implements MapperPluginInterface
{
    private EventDispatcherInterface $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @template T of object
     * @param T $event
     * @return T
     */
    public function dispatch(object $event): object
    {
        /** @phpstan-var T $e */
        $e = $this->dispatcher->dispatch($event);
        \assert($e instanceof $event);
        return $e;
    }
}
