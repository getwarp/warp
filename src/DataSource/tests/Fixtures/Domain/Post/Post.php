<?php

declare(strict_types=1);

namespace spaceonfire\DataSource\Fixtures\Domain\Post;

use spaceonfire\DataSource\AbstractEntity;

/**
 * @property string|null $id
 * @property string|null $title
 * @property string|null $authorId
 */
class Post extends AbstractEntity
{
    /**
     * @var string|null
     */
    private $id;
    /**
     * @var string|null
     */
    private $title;
    /**
     * @var string|null
     */
    private $authorId;

    /**
     * Post constructor.
     * @param string $id
     * @param string $title
     * @param string $authorId
     */
    public function __construct(string $id, string $title, string $authorId)
    {
        $this->setId($id);
        $this->setTitle($title);
        $this->setAuthorId($authorId);
    }

    /**
     * Getter for `id` property
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * Setter for `id` property
     * @param string|null $id
     * @return static
     */
    public function setId(?string $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Getter for `title` property
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Setter for `title` property
     * @param string|null $title
     * @return static
     */
    public function setTitle(?string $title): self
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Getter for `authorId` property
     * @return string|null
     */
    public function getAuthorId(): ?string
    {
        return $this->authorId;
    }

    /**
     * Setter for `authorId` property
     * @param string|null $authorId
     * @return static
     */
    public function setAuthorId(?string $authorId): self
    {
        $this->authorId = $authorId;
        return $this;
    }
}
