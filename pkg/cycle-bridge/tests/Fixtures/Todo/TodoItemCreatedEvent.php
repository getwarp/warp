<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Fixtures\Todo;

use Symfony\Contracts\EventDispatcher\Event;

final class TodoItemCreatedEvent extends Event
{
    private TodoItemId $id;

    public function __construct(TodoItemId $id)
    {
        $this->id = $id;
    }

    public function getId(): TodoItemId
    {
        return $this->id;
    }
}
