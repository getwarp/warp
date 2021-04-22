<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Fixtures;

use Cycle\ORM\Promise\PromiseInterface;

class Post
{
    public ?string $id = null;

    public ?string $title = null;

    public \DateTimeImmutable $createdAt;

    /**
     * @var User|PromiseInterface
     */
    public $author;

    public function __construct(string $id, string $title, User $author)
    {
        $this->setId($id);
        $this->setTitle($title);
        $this->setAuthor($author);
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(?string $id): void
    {
        $this->id = $id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function getAuthor(): User
    {
        if ($this->author instanceof PromiseInterface) {
            $this->author = $this->author->__resolve();
        }

        return $this->author;
    }

    public function setAuthor(User $author): void
    {
        $this->author = $author;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
