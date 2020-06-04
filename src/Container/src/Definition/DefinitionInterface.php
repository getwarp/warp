<?php

declare(strict_types=1);

namespace spaceonfire\Container\Definition;

use spaceonfire\Container\Argument\Argument;
use spaceonfire\Container\ContainerInterface;

interface DefinitionInterface
{
    /**
     * Get the alias of the definition.
     * @return string
     */
    public function getAbstract(): string;

    /**
     * Get the concrete of the definition.
     * @return mixed
     */
    public function getConcrete();

    /**
     * Is this a shared definition?
     * @return boolean
     */
    public function isShared(): bool;

    /**
     * Add an argument to be injected.
     * @param Argument $argument
     * @return $this
     */
    public function addArgument(Argument $argument): self;

    /**
     * Add multiple arguments to be injected.
     * @param Argument[] $arguments
     * @return $this
     */
    public function addArguments(array $arguments): self;

    /**
     * Add a method to be invoked
     * @param string $method
     * @param array<string,mixed> $arguments
     * @return $this
     */
    public function addMethodCall(string $method, array $arguments = []): self;

    /**
     * Add multiple methods to be invoked
     * @param array<string,array<string,mixed>> $methods
     * @return $this
     */
    public function addMethodCalls(array $methods = []): self;

    /**
     * Handle instantiation and manipulation of value and return.
     * @param ContainerInterface $container
     * @return mixed
     */
    public function resolve(ContainerInterface $container);
}
