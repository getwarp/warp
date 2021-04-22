<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Repository;

use Cycle\ORM\ORMInterface;
use Cycle\ORM\Promise\ReferenceInterface;
use spaceonfire\Bridge\Cycle\AbstractTestCase;
use spaceonfire\Bridge\Cycle\Fixtures\CsvFileFixtureLoader;
use spaceonfire\Bridge\Cycle\Fixtures\Mapper\PostMapper;
use spaceonfire\Bridge\Cycle\Fixtures\OrmCapsule;
use spaceonfire\Bridge\Cycle\Fixtures\Post;
use spaceonfire\Bridge\Cycle\Fixtures\User;
use spaceonfire\Collection\Map;
use spaceonfire\Criteria\Criteria;
use spaceonfire\Criteria\Expression\ExpressionFactory;
use spaceonfire\DataSource\EntityNotFoundException;

class CycleRepositoryTest extends AbstractTestCase
{
    private function makeRepository(ORMInterface $orm, string $role): AbstractRepository
    {
        return new class($role, $orm) extends AbstractRepository {
        };
    }

    /**
     * @dataProvider ormCapsuleProvider
     */
    public function testPersist(OrmCapsule $capsule): void
    {
        $repository = $this->makeRepository($capsule->orm(), 'post');

        $author = new User('35a60006-c34a-4c0b-8e9d-7759f6d0c09b', 'Admin User');

        $post = new Post(
            '0279d9bb-41e4-4fd0-ba05-87a2e112c7c2',
            'Yet another blog post',
            $author,
        );

        $repository->save($post);

        self::assertSame(
            1,
            $capsule->database()->select()
                ->from('post')
                ->where('id', '0279d9bb-41e4-4fd0-ba05-87a2e112c7c2')
                ->count()
        );
        self::assertSame(
            1,
            $capsule->database()->select()
                ->from('user')
                ->where('id', '35a60006-c34a-4c0b-8e9d-7759f6d0c09b')
                ->count()
        );

        $repository->remove($post);

        self::assertSame(
            0,
            $capsule->database()->select()
                ->from('post')
                ->where('id', '0279d9bb-41e4-4fd0-ba05-87a2e112c7c2')
                ->count()
        );
        self::assertSame(
            1,
            $capsule->database()->select()
                ->from('user')
                ->where('id', '35a60006-c34a-4c0b-8e9d-7759f6d0c09b')
                ->count()
        );
    }

    /**
     * @dataProvider ormCapsuleProvider
     */
    public function testFindByPrimary(OrmCapsule $capsule): void
    {
        $fixtureLoader = new CsvFileFixtureLoader($capsule->database(), __DIR__ . '/../Fixtures/config');
        $fixtureLoader->load('user.csv');
        $fixtureLoader->load('post.csv');

        /** @var AbstractRepository<Post,string> $repository */
        $repository = $this->makeRepository($capsule->orm(), 'post');

        /** @var Post $post */
        $post = $repository->findByPrimary(
            'be1cc1cc-0389-49c5-bb60-1368141b48b3',
            Criteria::new()->include(['author'])
        );

        self::assertSame('be1cc1cc-0389-49c5-bb60-1368141b48b3', $post->id);
        self::assertSame('New Post', $post->title);
        self::assertSame('2021-08-26 16:50:45', $post->createdAt->format('Y-m-d H:i:s'));
        self::assertInstanceOf(User::class, $post->author);
        self::assertNotInstanceOf(ReferenceInterface::class, $post->author);
    }

    /**
     * @dataProvider ormCapsuleProvider
     */
    public function testFindByPrimaryNotFound(OrmCapsule $capsule): void
    {
        $this->expectException(EntityNotFoundException::class);
        $repository = $this->makeRepository($capsule->orm(), 'post');
        $repository->findByPrimary('be1cc1cc-0389-49c5-bb60-1368141b48b3');
    }

    /**
     * @dataProvider ormCapsuleProvider
     */
    public function testFindAll(OrmCapsule $capsule): void
    {
        $fixtureLoader = new CsvFileFixtureLoader($capsule->database(), __DIR__ . '/../Fixtures/config');
        $fixtureLoader->load('user.csv');
        $fixtureLoader->load('post.csv');

        /** @var AbstractRepository<Post,string> $repository */
        $repository = $this->makeRepository($capsule->orm(), 'post');

        $posts = $repository->findAll(
            Criteria::new()
                ->orderBy([
                    'createdAt' => SORT_DESC,
                ])
                ->include(['author'])
        );

        self::assertCount(2, $posts);

        $posts = Map::new($posts);
        $firstPost = $posts->get(0);

        self::assertSame('be1cc1cc-0389-49c5-bb60-1368141b48b3', $firstPost->id);
        self::assertInstanceOf(User::class, $firstPost->author);
        self::assertNotInstanceOf(ReferenceInterface::class, $firstPost->author);

        $secondPost = $posts->get(1);

        self::assertSame('0de0e289-28a7-4944-953a-079d09ac4865', $secondPost->id);
        self::assertInstanceOf(User::class, $secondPost->author);
        self::assertNotInstanceOf(ReferenceInterface::class, $secondPost->author);
    }

    /**
     * @dataProvider ormCapsuleProvider
     */
    public function testFindOne(OrmCapsule $capsule): void
    {
        $fixtureLoader = new CsvFileFixtureLoader($capsule->database(), __DIR__ . '/../Fixtures/config');
        $fixtureLoader->load('user.csv');
        $fixtureLoader->load('post.csv');

        /** @var AbstractRepository<Post,string> $repository */
        $repository = $this->makeRepository($capsule->orm(), 'post');

        /** @var Post $post */
        $post = $repository->findOne(
            Criteria::new()
                ->orderBy([
                    'createdAt' => SORT_DESC,
                ])
                ->include(['author'])
        );

        self::assertSame('be1cc1cc-0389-49c5-bb60-1368141b48b3', $post->id);
        self::assertInstanceOf(User::class, $post->author);
        self::assertNotInstanceOf(ReferenceInterface::class, $post->author);
    }

    /**
     * @dataProvider ormCapsuleProvider
     */
    public function testCount(OrmCapsule $capsule): void
    {
        $fixtureLoader = new CsvFileFixtureLoader($capsule->database(), __DIR__ . '/../Fixtures/config');
        $fixtureLoader->load('user.csv');
        $fixtureLoader->load('post.csv');

        /** @var AbstractRepository<Post,string> $repository */
        $repository = $this->makeRepository($capsule->orm(), 'post');

        $expr = ExpressionFactory::new();

        self::assertSame(2, $repository->count());
        self::assertSame(1, $repository->count(Criteria::new()->where($expr->property('id', $expr->same('0de0e289-28a7-4944-953a-079d09ac4865')))));
    }

    /**
     * @dataProvider ormCapsuleProvider
     */
    public function testGetMapper(OrmCapsule $capsule): void
    {
        /** @var AbstractRepository<Post,string> $repository */
        $repository = $this->makeRepository($capsule->orm(), 'post');

        self::assertInstanceOf(PostMapper::class, $repository->getMapper());
    }
}
