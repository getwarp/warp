<?php

declare(strict_types=1);

namespace spaceonfire\Container\Argument;

use spaceonfire\Container\ContainerInterface;
use spaceonfire\Container\Exception\CannotInstantiateAbstractClassException;
use spaceonfire\Container\Exception\ContainerException;
use spaceonfire\Container\RawValueHolder;

final class Argument
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var string|null Argument class FQN or null for scalars or arrays
     */
    private $className;
    /**
     * @var RawValueHolder |null
     */
    private $defaultValue;

    /**
     * Argument constructor.
     * @param string $name
     * @param string|null $className
     * @param RawValueHolder |null $defaultValue
     */
    public function __construct(string $name, ?string $className = null, ?RawValueHolder $defaultValue = null)
    {
        $this->name = $name;
        $this->className = $className;
        $this->defaultValue = $defaultValue;
    }

    /**
     * Getter for `name` property.
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Resolve argument using container.
     * @param ContainerInterface $container
     * @return mixed
     */
    public function resolve(ContainerInterface $container)
    {
        if (null !== $this->className && $container->has($this->className)) {
            try {
                return $container->get($this->className);
            } catch (CannotInstantiateAbstractClassException $exception) {
                if (null !== $this->defaultValue) {
                    return $this->defaultValue->getValue();
                }

                throw $exception;
            }
        }

        if (null !== $this->defaultValue) {
            return $this->defaultValue->getValue();
        }

        throw new ContainerException('Unable to resolve argument');
    }
}
