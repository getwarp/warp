<?php

declare(strict_types=1);

namespace spaceonfire\DataSource;

use PHPUnit\Framework\TestCase;
use spaceonfire\Criteria\Criteria;
use spaceonfire\DataSource\Fixtures\Domain\Exceptions\PostNotFoundException;
use spaceonfire\DataSource\Fixtures\Domain\Post;
use spaceonfire\DataSource\Fixtures\Infrastructure\Mapper\StubMapper;
use spaceonfire\DataSource\Fixtures\Infrastructure\Persistence\InMemoryPostRepository;
use Webmozart\Expression\Expr;

class RepositoryTest extends TestCase
{
    /**
     * @var RepositoryInterface
     */
    private static $repository;

    protected function setUp(): void
    {
        if (self::$repository === null) {
            self::$repository = new InMemoryPostRepository([
                1 => new Post(1, 'Hello, World!'),
                2 => new Post(2, 'New Post'),
            ]);
        }
    }

    public function testSave(): void
    {
        self::$repository->save(new Post(3, 'Yet another blog post'));
        self::assertTrue(true);
    }

    /**
     * @depends testSave
     */
    public function testFindByPrimary(): void
    {
        $post = self::$repository->findByPrimary(3);
        self::assertEquals(3, $post['id']);
        self::assertEquals('Yet another blog post', $post['title']);
    }

    /**
     * @depends testSave
     */
    public function testFindAll(): void
    {
        $posts = self::$repository->findAll(new Criteria());
        self::assertCount(3, $posts);
        foreach ($posts as $post) {
            self::assertInstanceOf(Post::class, $post);
        }
    }

    /**
     * @depends testSave
     */
    public function testFindOne(): void
    {
        $post = self::$repository->findOne(
            (new Criteria())->where(Expr::property('title', Expr::equals('Hello, World!')))
        );

        self::assertEquals(1, $post['id']);
        self::assertEquals('Hello, World!', $post['title']);
    }

    /**
     * @depends testFindByPrimary
     */
    public function testRemove(): void
    {
        $post = self::$repository->findByPrimary(3);
        self::$repository->remove($post);

        $this->expectException(PostNotFoundException::class);
        self::$repository->findByPrimary(3);
    }

    public function testGetMapper(): void
    {
        self::assertInstanceOf(StubMapper::class, self::$repository->getMapper());
    }
}
