<?php

declare(strict_types=1);

namespace Warp\DataSource\Exceptions;

class NotFoundException extends DomainException
{
    /**
     * @inheritDoc
     */
    protected function getDefaultMessage(array $parameters = []): string
    {
        return isset($parameters['{primary}'])
            ? 'Entity not found by primary "{primary}"'
            : 'Entity not found';
    }
}
