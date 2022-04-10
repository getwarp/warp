<?php

declare(strict_types=1);

namespace Warp\DataSource\Fixtures;

class Post
{
    private ?string $id = null;
    private ?string $title = null;
    private ?string $authorId = null;

    public function __construct(string $id, string $title, string $authorId)
    {
        $this->setId($id);
        $this->setTitle($title);
        $this->setAuthorId($authorId);
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

    public function getAuthorId(): ?string
    {
        return $this->authorId;
    }

    public function setAuthorId(?string $authorId): void
    {
        $this->authorId = $authorId;
    }
}
