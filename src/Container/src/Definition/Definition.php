<?php

declare(strict_types=1);

namespace spaceonfire\Container\Definition;

use spaceonfire\Container\Argument\Argument;
use spaceonfire\Container\ContainerInterface;
use spaceonfire\Container\Exception\ContainerException;

final class Definition implements DefinitionInterface
{
    /**
     * @var string
     */
    private $abstract;
    /**
     * @var mixed
     */
    private $concrete;
    /**
     * @var bool
     */
    private $shared;
    /**
     * @var Argument[]
     */
    private $arguments = [];
    /**
     * @var array[]
     */
    private $methods = [];
    /**
     * @var object|null
     */
    private $resolved;

    /**
     * Definition constructor.
     * @param string $abstract
     * @param mixed|null $concrete
     * @param bool $shared
     */
    public function __construct(string $abstract, $concrete = null, bool $shared = false)
    {
        $concrete = $concrete ?? $abstract;

        if (is_object($concrete) && !is_callable($concrete)) {
            $shared = true;
        }

        $this->abstract = $abstract;
        $this->concrete = $concrete;
        $this->shared = $shared;
    }

    /**
     * @inheritDoc
     */
    public function getAbstract(): string
    {
        return $this->abstract;
    }

    /**
     * @inheritDoc
     */
    public function getConcrete()
    {
        return $this->concrete;
    }

    /**
     * @inheritDoc
     */
    public function isShared(): bool
    {
        return $this->shared;
    }

    /**
     * @inheritDoc
     */
    public function addArgument(Argument $argument): DefinitionInterface
    {
        $this->arguments[$argument->getName()] = $argument;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addArguments(array $arguments): DefinitionInterface
    {
        foreach ($arguments as $argument) {
            $this->addArgument($argument);
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addMethodCall(string $method, array $arguments = []): DefinitionInterface
    {
        $this->methods[] = [$method, $arguments];
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addMethodCalls(array $methods = []): DefinitionInterface
    {
        foreach ($methods as $method => $arguments) {
            $this->addMethodCall($method, $arguments);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function resolve(ContainerInterface $container)
    {
        if ($this->resolved !== null && $this->isShared()) {
            return $this->resolved;
        }

        if (is_callable($this->concrete)) {
            $resolved = $container->invoke($this->concrete, $this->arguments);
        } elseif (is_string($this->concrete)) {
            $buildAnyway = $this->abstract === $this->concrete || $this->arguments !== [];

            $resolved = $buildAnyway
                ? $container->make($this->concrete, $this->arguments)
                : $container->get($this->concrete);
        } elseif (is_object($this->concrete)) {
            $resolved = $this->concrete;
        }

        if (!isset($resolved)) {
            throw new ContainerException('Unable to resolve definition');
        }

        if (is_object($resolved)) {
            foreach ($this->methods as [$method, $arguments]) {
                $container->invoke([$resolved, $method], $arguments);
            }
        }

        return $this->resolved = $resolved;
    }
}
