<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Mapper\Plugin;

use Symfony\Contracts\EventDispatcher\Event;

final class HydrateAfterEvent extends Event
{
    private object $entity;

    /**
     * @var array<string,mixed>
     */
    private array $data;

    /**
     * @param array<string,mixed> $data
     */
    public function __construct(object $entity, array $data)
    {
        $this->entity = $entity;
        $this->data = $data;
    }

    public function getEntity(): object
    {
        return $this->entity;
    }

    /**
     * @return array<string,mixed>
     */
    public function getData(): array
    {
        return $this->data;
    }
}
