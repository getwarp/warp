<?php

declare(strict_types=1);

namespace spaceonfire\DataSource\Fixtures\Domain;

use spaceonfire\DataSource\EntityInterface;

class Post implements EntityInterface
{
    /**
     * @var array<mixed>
     */
    private $data;

    /**
     * Post constructor.
     * @param int $id
     * @param string $title
     */
    public function __construct(int $id, string $title)
    {
        $this->data = compact('id', 'title');
    }

    /**
     * @inheritDoc
     * @param string $offset
     */
    public function offsetExists($offset): bool
    {
        return isset($this->data[$offset]);
    }

    /**
     * @inheritDoc
     * @param string $offset
     * @return mixed|null
     */
    public function offsetGet($offset)
    {
        return $this->data[$offset] ?? null;
    }

    /**
     * @inheritDoc
     * @param string $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value): void
    {
        $this->data[$offset] = $value;
    }

    /**
     * @inheritDoc
     * @param string $offset
     */
    public function offsetUnset($offset): void
    {
        unset($this->data[$offset]);
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        return $this->data;
    }
}
