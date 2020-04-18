<?php

declare(strict_types=1);

namespace spaceonfire\DataSource;

use JsonException;
use PHPUnit\Framework\TestCase;
use spaceonfire\DataSource\Fixtures\Domain\Post;

class EntityTest extends TestCase
{
    /**
     * @var Post
     */
    private $entity;

    protected function setUp(): void
    {
        $this->entity = new Post(1, 'Hello, World!');
    }

    public function testOffsetGet(): void
    {
        self::assertEquals(1, $this->entity['id']);
        self::assertEquals('Hello, World!', $this->entity['title']);
        self::assertNull($this->entity['unknown_property']);
    }

    public function testOffsetSet(): void
    {
        $this->entity['id'] = 2;
        $this->entity['title'] = 'New Post Title';
        self::assertEquals(2, $this->entity['id']);
        self::assertEquals('New Post Title', $this->entity['title']);
    }

    public function testOffsetExists(): void
    {
        self::assertTrue(isset($this->entity['id']));
        self::assertTrue(isset($this->entity['title']));
        self::assertFalse(isset($this->entity['unknown_property']));
    }

    public function testOffsetUnset(): void
    {
        self::assertEquals(1, $this->entity['id']);
        unset($this->entity['id']);
        self::assertFalse(isset($this->entity['id']));

        self::assertEquals('Hello, World!', $this->entity['title']);
        unset($this->entity['title']);
        self::assertFalse(isset($this->entity['title']));
    }

    /**
     * @throws JsonException
     */
    public function testJsonSerialize(): void
    {
        self::assertJsonStringEqualsJsonString(
            json_encode(['id' => 1, 'title' => 'Hello, World!'], JSON_THROW_ON_ERROR),
            json_encode($this->entity, JSON_THROW_ON_ERROR)
        );
    }
}
