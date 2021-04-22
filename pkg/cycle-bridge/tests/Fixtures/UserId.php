<?php

/** @noinspection MagicMethodsValidityInspection */

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Fixtures;

use Cycle\ORM\Promise\ReferenceInterface;

final class UserId implements \Stringable, ReferenceInterface
{
    private string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function __toString(): string
    {
        return $this->id;
    }

    public function __role(): string
    {
        return 'user';
    }

    public function __scope(): array
    {
        return [
            'id' => $this->id,
        ];
    }
}
