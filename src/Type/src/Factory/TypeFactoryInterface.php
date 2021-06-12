<?php

declare(strict_types=1);

namespace spaceonfire\Type\Factory;

use spaceonfire\Type\TypeInterface;

interface TypeFactoryInterface
{
    /**
     * Check that given string can be used to create a new Type instance.
     * @param string $type
     * @return bool
     */
    public function supports(string $type): bool;

    /**
     * Create new Type instance from a string.
     * @param string $type
     * @return TypeInterface
     */
    public function make(string $type): TypeInterface;

    /**
     * Set parent factory.
     * @param TypeFactoryInterface $parent
     */
    public function setParent(self $parent): void;
}
