<?php

declare(strict_types=1);

namespace Warp\Bridge\Cycle\Fixtures\Todo;

use Warp\Bridge\Cycle\Fixtures\User;
use Warp\DataSource\Blame\BlamableInterface;
use Warp\DataSource\Blame\Blame;
use Warp\DataSource\Blame\BlameImmutable;
use Warp\DataSource\Blame\BlameImmutableInterface;
use Warp\DataSource\Blame\BlameInterface;
use Warp\DataSource\EntityEventsInterface;
use Warp\DataSource\EntityEventsTrait;

/**
 * @implements BlamableInterface<User>
 */
class TodoItem implements BlamableInterface, EntityEventsInterface
{
    use EntityEventsTrait;

    private TodoItemId $id;

    private string $content;

    private bool $done;

    /**
     * @var BlameInterface<User>
     */
    private BlameInterface $blame;

    public function __construct(?TodoItemId $id, string $content)
    {
        $this->id = $id ?? TodoItemId::random();
        $this->content = $content;
        $this->done = false;
        $this->blame = Blame::new(User::class);
        $this->recordEvent(new TodoItemCreatedEvent($this->id));
    }

    public function getId(): TodoItemId
    {
        return $this->id;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function isDone(): bool
    {
        return $this->done;
    }

    public function replaceContent(string $content): void
    {
        if ($content === $this->content) {
            return;
        }

        $this->content = $content;
        $this->blame(null, true);
    }

    public function markDone(): void
    {
        if ($this->done) {
            return;
        }

        $this->done = true;
        $this->blame(null, true);
        $this->recordEvent(new TodoItemDoneEvent($this->id));
    }

    public function unmarkDone(): void
    {
        if (!$this->done) {
            return;
        }

        $this->done = false;
        $this->blame(null, true);
        $this->recordEvent(new TodoItemUndoneEvent($this->id));
    }

    public function getBlame(): BlameImmutableInterface
    {
        return new BlameImmutable($this->blame);
    }

    public function blame(?object $by, bool $force = false): void
    {
        $force = $force || $this->blame->isNew();

        if (!$force && $this->blame->isTouched()) {
            return;
        }

        $this->blame->touch($by);
    }
}
