<?php

declare(strict_types=1);

namespace Warp\Container;

use Psr\Container\ContainerInterface;

interface GetWithOptionsMethodInterface extends ContainerInterface
{
    /**
     * @inheritDoc
     * @param FactoryOptionsInterface|array<string,mixed>|null $options
     */
    public function get(string $id, $options = null);
}
