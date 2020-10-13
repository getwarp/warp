<?php

declare(strict_types=1);

namespace spaceonfire\Type\Factory;

use spaceonfire\Type\Type;

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
     * @return Type
     */
    public function make(string $type): Type;

    /**
     * Set parent factory.
     * @param TypeFactoryInterface $parent
     */
    public function setParent(self $parent): void;
}
