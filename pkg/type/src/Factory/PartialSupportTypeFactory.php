<?php

declare(strict_types=1);

namespace spaceonfire\Type\Factory;

use spaceonfire\Type\Exception\TypeNotSupportedException;
use spaceonfire\Type\TypeInterface;

final class PartialSupportTypeFactory implements TypeFactoryInterface
{
    use TypeFactoryTrait;

    private TypeFactoryInterface $factory;

    /**
     * @var callable
     */
    private $supportedPredicate;

    /**
     * @param TypeFactoryInterface $factory
     * @param callable $supportedPredicate
     */
    public function __construct(TypeFactoryInterface $factory, callable $supportedPredicate)
    {
        $this->factory = $factory;
        $this->supportedPredicate = $supportedPredicate;
    }

    public function supports(string $type): bool
    {
        $type = $this->removeWhitespaces($type);
        return $this->factory->supports($type) && ($this->supportedPredicate)($type);
    }

    public function make(string $type): TypeInterface
    {
        $type = $this->removeWhitespaces($type);

        if (!$this->supports($type)) {
            throw new TypeNotSupportedException($type);
        }

        return $this->factory->make($type);
    }
}
