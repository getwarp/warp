<?php

declare(strict_types=1);

namespace spaceonfire\DataSource\Fixtures;

class User
{
    private ?string $id = null;
    private ?string $name = null;

    public function __construct(string $id, string $name)
    {
        $this->setId($id);
        $this->setName($name);
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(?string $id): void
    {
        $this->id = $id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }
}
