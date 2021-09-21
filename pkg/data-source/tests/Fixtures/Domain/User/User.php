<?php

declare(strict_types=1);

namespace spaceonfire\DataSource\Fixtures\Domain\User;

use spaceonfire\DataSource\AbstractEntity;

/**
 * @codeCoverageIgnore
 * @property string|null $id
 * @property string|null $name
 */
class User extends AbstractEntity
{
    /**
     * @var string|null
     */
    private $id;
    /**
     * @var string|null
     */
    private $name;

    /**
     * User constructor.
     * @param string $id
     * @param string $name
     */
    public function __construct(string $id, string $name)
    {
        $this->setId($id);
        $this->setName($name);
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
     * Getter for `name` property
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Setter for `name` property
     * @param string|null $name
     * @return static
     */
    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }
}
