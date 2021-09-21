<?php

declare(strict_types=1);

namespace spaceonfire\DataSource\Exceptions;

class RemoveException extends DomainException
{
    /**
     * @inheritDoc
     */
    protected function getDefaultMessage(array $parameters = []): string
    {
        return 'Remove Exception';
    }
}
