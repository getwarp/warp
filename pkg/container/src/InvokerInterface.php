<?php

declare(strict_types=1);

namespace spaceonfire\Container;

interface InvokerInterface
{
    /**
     * Invoke a callable via the container.
     * @param callable $callable
     * @param InvokerOptionsInterface|array<string,mixed>|null $options
     * @return mixed
     */
    public function invoke(callable $callable, $options = null);
}
