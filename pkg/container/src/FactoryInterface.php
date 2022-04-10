<?php

declare(strict_types=1);

namespace Warp\Container;

/**
 * @template T
 */
interface FactoryInterface
{
    /**
     * @param FactoryOptionsInterface|null $options
     * @return T
     */
    public function make(?FactoryOptionsInterface $options = null);
}
