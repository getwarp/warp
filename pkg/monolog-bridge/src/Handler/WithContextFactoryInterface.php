<?php

declare(strict_types=1);

namespace spaceonfire\MonologBridge\Handler;

interface WithContextFactoryInterface
{
    /**
     * @param ContextHandlerFactoryInterface $factory
     * @return static cloned instance
     */
    public function withContextFactory(ContextHandlerFactoryInterface $factory): self;
}
