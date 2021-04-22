<?php

declare(strict_types=1);

namespace spaceonfire\Type\Exception;

final class TypeNotSupportedException extends \InvalidArgumentException
{
    /**
     * TypeNotSupportedException constructor.
     * @param string $type
     * @param string|null $typeClass
     */
    public function __construct(string $type, ?string $typeClass = null)
    {
        parent::__construct(\sprintf('Type "%s" is not supported%s', $type, $typeClass ? 'by ' . $typeClass : ''));
    }
}
