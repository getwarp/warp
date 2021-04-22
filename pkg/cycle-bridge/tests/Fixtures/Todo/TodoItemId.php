<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Fixtures\Todo;

use Cycle\ORM\Promise\ReferenceInterface;
use spaceonfire\ValueObject\UuidValue;

final class TodoItemId extends UuidValue implements ReferenceInterface
{
    public const ROLE = 'todo_item';

    public function __role(): string
    {
        return self::ROLE;
    }

    public function __scope(): array
    {
        return [
            'id' => $this->value,
        ];
    }
}
