<?php

declare(strict_types=1);

namespace spaceonfire\Type\Factory;

use spaceonfire\Type\Exception\TypeNotSupportedException;
use spaceonfire\Type\Type;

final class PartialSupportTypeFactory implements TypeFactoryInterface
{
    use TypeFactoryTrait;

    /**
     * @var TypeFactoryInterface
     */
    private $factory;
    /**
     * @var callable
     */
    private $supportedPredicate;

    /**
     * PartialSupportTypeFactory constructor.
     * @param TypeFactoryInterface $factory
     * @param callable $supportedPredicate
     */
    public function __construct(TypeFactoryInterface $factory, callable $supportedPredicate)
    {
        $this->factory = $factory;
        $this->supportedPredicate = $supportedPredicate;
    }

    /**
     * @inheritDoc
     */
    public function supports(string $type): bool
    {
        $type = $this->removeWhitespaces($type);
        return $this->factory->supports($type) && ($this->supportedPredicate)($type);
    }

    /**
     * @inheritDoc
     */
    public function make(string $type): Type
    {
        $type = $this->removeWhitespaces($type);

        if (!$this->supports($type)) {
            throw new TypeNotSupportedException($type);
        }

        return $this->factory->make($type);
    }
}
