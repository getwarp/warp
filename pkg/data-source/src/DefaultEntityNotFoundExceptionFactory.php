<?php

declare(strict_types=1);

namespace spaceonfire\DataSource;

final class DefaultEntityNotFoundExceptionFactory implements EntityNotFoundExceptionFactoryInterface
{
    public function make(string $entity, $primary): EntityNotFoundException
    {
        return EntityNotFoundException::byPrimary($entity, $primary);
    }
}
