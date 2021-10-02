<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Monolog\Handler;

use Monolog\Handler\HandlerInterface;
use spaceonfire\Bridge\Monolog\Exception\UnknownHandlerTypeException;

/**
 * @implements \IteratorAggregate<HandlerFactoryInterface>
 */
final class HandlerFactoryAggregate implements \IteratorAggregate, ContextHandlerFactoryInterface
{
    /**
     * @var array<string,HandlerFactoryInterface>
     */
    private array $factories = [];

    public function __construct(HandlerFactoryInterface ...$factories)
    {
        foreach ($factories as $factory) {
            $this->add($factory);
        }
    }

    public function add(HandlerFactoryInterface $factory): void
    {
        $factory = $factory instanceof WithContextFactoryInterface ? $factory->withContextFactory($this) : $factory;

        foreach ($factory->supportedTypes() as $type) {
            $this->factories[$type] = $factory;
        }
    }

    public function supports(string $handlerType): bool
    {
        return isset($this->factories[$handlerType]);
    }

    public function get(string $handlerType): HandlerFactoryInterface
    {
        if (!isset($this->factories[$handlerType])) {
            throw UnknownHandlerTypeException::forHandlerType($handlerType, \array_keys($this->factories));
        }

        return $this->factories[$handlerType];
    }

    public function make(string $context, array $settings): HandlerInterface
    {
        return $this->get($context)->make($settings);
    }

    /**
     * @return \Generator<HandlerFactoryInterface>
     */
    public function getIterator(): \Generator
    {
        $yielded = [];

        foreach ($this->factories as $factory) {
            $id = \spl_object_id($factory);
            if (isset($yielded[$id])) {
                continue;
            }

            yield $factory;
            $yielded[$id] = true;
        }
    }
}
