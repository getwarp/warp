<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Fixtures\Todo;

final class TodoItemDoneEvent
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
