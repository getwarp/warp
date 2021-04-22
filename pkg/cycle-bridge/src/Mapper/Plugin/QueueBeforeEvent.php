<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Mapper\Plugin;

use Cycle\ORM\Heap\Node;
use Cycle\ORM\Heap\State;
use Symfony\Contracts\EventDispatcher\Event;

final class QueueBeforeEvent extends Event
{
    private object $entity;

    private Node $node;

    private State $state;

    public function __construct(object $entity, Node $node, State $state)
    {
        $this->entity = $entity;
        $this->node = $node;
        $this->state = $state;
    }

    public function getEntity(): object
    {
        return $this->entity;
    }

    public function getNode(): Node
    {
        return $this->node;
    }

    public function getState(): State
    {
        return $this->state;
    }
}
