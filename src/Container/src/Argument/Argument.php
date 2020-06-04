<?php

declare(strict_types=1);

namespace spaceonfire\Container\Argument;

use spaceonfire\Container\ContainerInterface;
use spaceonfire\Container\Exception\ContainerException;

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
     * @var ArgumentValue|null
     */
    private $defaultValue;

    /**
     * Argument constructor.
     * @param string $name
     * @param string|null $className
     * @param ArgumentValue|null $defaultValue
     */
    public function __construct(string $name, ?string $className = null, ?ArgumentValue $defaultValue = null)
    {
        $this->name = $name;
        $this->className = $className;
        $this->defaultValue = $defaultValue;
    }

    /**
     * Getter for `name` property
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Resolve argument using container
     * @param ContainerInterface $container
     * @return mixed
     */
    public function resolve(ContainerInterface $container)
    {
        if ($this->className !== null && $container->has($this->className)) {
            return $container->get($this->className);
        }

        if ($this->defaultValue !== null) {
            return $this->defaultValue->getValue();
        }

        throw new ContainerException('Unable to resolve argument');
    }
}
