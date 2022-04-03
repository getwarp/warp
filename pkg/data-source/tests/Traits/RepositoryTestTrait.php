<?php

declare(strict_types=1);

namespace Warp\DataSource\Traits;

use Warp\Criteria\Criteria;
use Warp\DataSource\Fixtures\Domain\Post\Exceptions\PostNotFoundException;
use Warp\DataSource\Fixtures\Domain\Post\Post;
use Warp\DataSource\RepositoryInterface;

trait RepositoryTestTrait
{
    /**
     * @var RepositoryInterface
     */
    protected static $repository;

    public function testSave(): void
    {
        self::$repository->save(
            new Post(
                '0279d9bb-41e4-4fd0-ba05-87a2e112c7c2',
                'Yet another blog post',
                '35a60006-c34a-4c0b-8e9d-7759f6d0c09b'
            )
        );
        self::assertTrue(true);
    }

    /**
     * @depends testSave
     */
    public function testFindByPrimary(): void
    {
        $post = self::$repository->findByPrimary('0279d9bb-41e4-4fd0-ba05-87a2e112c7c2');
        self::assertEquals('0279d9bb-41e4-4fd0-ba05-87a2e112c7c2', $post['id']);
        self::assertEquals('Yet another blog post', $post['title']);
    }

    /**
     * @depends testSave
     */
    public function testFindAll(): void
    {
        $posts = self::$repository->findAll();
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
            (new Criteria())->where(Criteria::expr()->property('title', Criteria::expr()->equals('Hello, World!')))
        );

        self::assertEquals('0de0e289-28a7-4944-953a-079d09ac4865', $post['id']);
        self::assertEquals('Hello, World!', $post['title']);
    }

    /**
     * @depends testSave
     */
    public function testCount(): void
    {
        self::assertEquals(3, self::$repository->count());
    }

    /**
     * @depends testFindByPrimary
     */
    public function testRemove(): void
    {
        $post = self::$repository->findByPrimary('0279d9bb-41e4-4fd0-ba05-87a2e112c7c2');
        self::$repository->remove($post);

        $this->expectException(PostNotFoundException::class);
        self::$repository->findByPrimary('0279d9bb-41e4-4fd0-ba05-87a2e112c7c2');
    }
}
