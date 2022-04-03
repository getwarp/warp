<?php

declare(strict_types=1);

namespace Warp\DataSource;

use Nette\Utils\Json;
use Nette\Utils\JsonException;
use PHPUnit\Framework\TestCase;
use Warp\DataSource\Fixtures\Domain\Post\Post;

class EntityTest extends TestCase
{
    /**
     * @var Post
     */
    private $entity;

    protected function setUp(): void
    {
        $this->entity = new Post(
            '0de0e289-28a7-4944-953a-079d09ac4865',
            'Hello, World!',
            '35a60006-c34a-4c0b-8e9d-7759f6d0c09b'
        );
    }

    public function testOffsetGet(): void
    {
        self::assertEquals('0de0e289-28a7-4944-953a-079d09ac4865', $this->entity['id']);
        self::assertEquals('Hello, World!', $this->entity['title']);
    }

    public function testOffsetSet(): void
    {
        $this->entity['id'] = 'be1cc1cc-0389-49c5-bb60-1368141b48b3';
        $this->entity['title'] = 'New Post Title';
        self::assertEquals('be1cc1cc-0389-49c5-bb60-1368141b48b3', $this->entity['id']);
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
        self::assertEquals('0de0e289-28a7-4944-953a-079d09ac4865', $this->entity['id']);
        unset($this->entity['id']);
        self::assertNull($this->entity['id']);

        self::assertEquals('Hello, World!', $this->entity['title']);
        unset($this->entity['title']);
        self::assertNull($this->entity['title']);
    }

    /**
     * @throws JsonException
     */
    public function testJsonSerialize(): void
    {
        self::assertJsonStringEqualsJsonString(
            Json::encode([
                'id' => '0de0e289-28a7-4944-953a-079d09ac4865',
                'title' => 'Hello, World!',
                'authorId' => '35a60006-c34a-4c0b-8e9d-7759f6d0c09b',
            ]),
            Json::encode($this->entity)
        );
    }
}
