<?php

declare(strict_types=1);

namespace Warp\Container;

interface FactoryOptionsInterface extends InvokerOptionsInterface
{
    public function getStaticConstructor(): ?string;

    public function setStaticConstructor(string $constructor): self;

    public function addMethodCall(string $method, ?InvokerOptionsInterface $options = null): self;

    /**
     * @return iterable<array{string,InvokerOptionsInterface|null}>
     */
    public function getMethodCalls(): iterable;
}
