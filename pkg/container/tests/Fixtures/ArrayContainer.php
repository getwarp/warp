<?php

declare(strict_types=1);

namespace Warp\Container\Fixtures;

use Psr\Container\ContainerInterface;
use Warp\Container\Exception\NotFoundException;

final class ArrayContainer implements ContainerInterface
{
    private array $services;

    public function __construct(array $services)
    {
        $this->services = $services;
    }

    public function get(string $id)
    {
        if (isset($this->services[$id])) {
            return $this->services[$id];
        }

        throw NotFoundException::alias($id);
    }

    public function has(string $id): bool
    {
        return isset($this->services[$id]);
    }
}
