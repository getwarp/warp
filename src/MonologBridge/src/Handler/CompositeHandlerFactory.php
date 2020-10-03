<?php

declare(strict_types=1);

namespace spaceonfire\MonologBridge\Handler;

use Monolog\Handler\HandlerInterface;
use spaceonfire\MonologBridge\Exception\UnknownHandlerTypeException;

final class CompositeHandlerFactory
{
    /**
     * @var array<string,HandlerFactoryInterface[]>
     */
    private $factoriesPerType = [];

    /**
     * CompositeHandlerFactory constructor.
     * @param HandlerFactoryInterface[] $factories
     */
    public function __construct(iterable $factories = [])
    {
        foreach ($factories as $factory) {
            $this->add($factory);
        }
    }

    public function add(HandlerFactoryInterface $factory): self
    {
        foreach ($factory->supportedTypes() as $type) {
            if (isset($this->factoriesPerType[$type])) {
                $this->factoriesPerType[$type][] = $factory;
            } else {
                $this->factoriesPerType[$type] = [$factory];
            }
        }

        return $this;
    }

    public function supports(string $handlerType): bool
    {
        return isset($this->factoriesPerType[$handlerType]);
    }

    /**
     * @param string $handlerType
     * @param array<string,mixed> $parameters
     * @return HandlerInterface
     */
    public function make(string $handlerType, array $parameters): HandlerInterface
    {
        if (!isset($this->factoriesPerType[$handlerType])) {
            throw UnknownHandlerTypeException::forHandlerType($handlerType);
        }

        /** @var HandlerFactoryInterface $factory */
        $factory = end($this->factoriesPerType[$handlerType]);

        return $factory->make($parameters, $this);
    }
}
