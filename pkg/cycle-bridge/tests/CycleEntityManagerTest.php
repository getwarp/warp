<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle;

use Cycle\ORM\Promise\ReferenceInterface;
use Cycle\ORM\TransactionInterface;
use spaceonfire\Bridge\Cycle\Fixtures\CsvFileFixtureLoader;
use spaceonfire\Bridge\Cycle\Fixtures\OrmCapsule;
use spaceonfire\Bridge\Cycle\Fixtures\Post;
use spaceonfire\Bridge\Cycle\Fixtures\Todo\TodoItem;
use spaceonfire\Bridge\Cycle\Fixtures\User;
use spaceonfire\Bridge\Cycle\Fixtures\UserId;
use spaceonfire\Collection\Map;
use spaceonfire\Criteria\Criteria;
use spaceonfire\Criteria\Expression\ExpressionFactory;
use spaceonfire\DataSource\EntityNotFoundException;
use spaceonfire\ValueObject\Date\DateTimeImmutableValue;

/**
 * @todo: add test with both criteria and source scope applied
 */
class CycleEntityManagerTest extends AbstractTestCase
{
    /**
     * @dataProvider ormCapsuleProvider
     */
    public function testPersisterCascade(OrmCapsule $capsule): void
    {
        $em = new CycleEntityManager($capsule->orm(), TransactionInterface::MODE_CASCADE);

        $author = new User('35a60006-c34a-4c0b-8e9d-7759f6d0c09b', 'Admin User');

        $post = new Post(
            '0279d9bb-41e4-4fd0-ba05-87a2e112c7c2',
            'Yet another blog post',
            $author,
        );

        $em->save($post);

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

        $em->remove($post);

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
    public function testPersisterEntityOnly(OrmCapsule $capsule): void
    {
        $em = new CycleEntityManager($capsule->orm(), TransactionInterface::MODE_ENTITY_ONLY);

        $author = new User('35a60006-c34a-4c0b-8e9d-7759f6d0c09b', 'Admin User');

        $post = new Post(
            '0279d9bb-41e4-4fd0-ba05-87a2e112c7c2',
            'Yet another blog post',
            $author,
        );

        $em->save($author, $post);

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

        $post->setTitle('Yet another blog post 2');
        $author->setName('New Name');

        $em->save($post);

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
        self::assertSame(
            'Admin User',
            $capsule->database()->select('name')
                ->from('user')
                ->where('id', '35a60006-c34a-4c0b-8e9d-7759f6d0c09b')
                ->run()
                ->fetchColumn()
        );
        self::assertSame(
            'Yet another blog post 2',
            $capsule->database()->select('title')
                ->from('post')
                ->where('id', '0279d9bb-41e4-4fd0-ba05-87a2e112c7c2')
                ->run()
                ->fetchColumn()
        );
    }

    /**
     * @dataProvider ormCapsuleProvider
     */
    public function testReaderFindByPrimary(OrmCapsule $capsule): void
    {
        $fixtureLoader = new CsvFileFixtureLoader($capsule->database());
        $fixtureLoader->load(__DIR__ . '/Fixtures/config/user.csv');
        $fixtureLoader->load(__DIR__ . '/Fixtures/config/post.csv');

        $em = new CycleEntityManager($capsule->orm());

        /** @var Post $post */
        $post = $em->getReader(Post::class)->findByPrimary(
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
    public function testReaderFindByPrimaryNotFound(OrmCapsule $capsule): void
    {
        $this->expectException(EntityNotFoundException::class);
        $em = new CycleEntityManager($capsule->orm());
        $em->getReader(Post::class)->findByPrimary(
            'be1cc1cc-0389-49c5-bb60-1368141b48b3',
            Criteria::new()->include(['author'])
        );
    }

    /**
     * @dataProvider ormCapsuleProvider
     */
    public function testReaderFindAll(OrmCapsule $capsule): void
    {
        $fixtureLoader = new CsvFileFixtureLoader($capsule->database());
        $fixtureLoader->load(__DIR__ . '/Fixtures/config/user.csv');
        $fixtureLoader->load(__DIR__ . '/Fixtures/config/post.csv');

        $em = new CycleEntityManager($capsule->orm());
        $posts = $em->getReader(Post::class)->findAll(
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
    public function testReaderFindAllWithWhere(OrmCapsule $capsule): void
    {
        $fixtureLoader = new CsvFileFixtureLoader($capsule->database());
        $fixtureLoader->load(__DIR__ . '/Fixtures/config/user.csv');
        $fixtureLoader->load(__DIR__ . '/Fixtures/config/post.csv');

        $em = new CycleEntityManager($capsule->orm());
        $ef = ExpressionFactory::new();
        $posts = $em->getReader(Post::class)->findAll(
            Criteria::new()
                ->where($ef->property('author.name', $ef->startsWith('Admin')))
                ->orderBy([
                    'createdAt' => SORT_DESC,
                ])
        );

        self::assertCount(2, $posts);

        $posts = Map::new($posts);
        $firstPost = $posts->get(0);

        self::assertSame('be1cc1cc-0389-49c5-bb60-1368141b48b3', $firstPost->id);
        self::assertInstanceOf(ReferenceInterface::class, $firstPost->author);

        $secondPost = $posts->get(1);

        self::assertSame('0de0e289-28a7-4944-953a-079d09ac4865', $secondPost->id);
        self::assertInstanceOf(ReferenceInterface::class, $secondPost->author);
    }

    /**
     * @dataProvider ormCapsuleProvider
     */
    public function testReaderCount(OrmCapsule $capsule): void
    {
        $fixtureLoader = new CsvFileFixtureLoader($capsule->database());
        $fixtureLoader->load(__DIR__ . '/Fixtures/config/user.csv');
        $fixtureLoader->load(__DIR__ . '/Fixtures/config/post.csv');

        $em = new CycleEntityManager($capsule->orm());

        $expr = ExpressionFactory::new();

        self::assertSame(2, $em->getReader(Post::class)->count());
        self::assertSame(1, $em->getReader(Post::class)->count(Criteria::new()->where($expr->property('id', $expr->same('0de0e289-28a7-4944-953a-079d09ac4865')))));
        self::assertSame(1, $em->getReader(User::class)->count());
    }

    /**
     * @dataProvider ormCapsuleProvider
     */
    public function testReaderFindAllByUser(OrmCapsule $capsule): void
    {
        $fixtureLoader = new CsvFileFixtureLoader($capsule->database());
        $fixtureLoader->load(__DIR__ . '/Fixtures/config/user.csv');
        $fixtureLoader->load(__DIR__ . '/Fixtures/config/post.csv');

        $em = new CycleEntityManager($capsule->orm());
        $user = $em->getReader(User::class)->findByPrimary('35a60006-c34a-4c0b-8e9d-7759f6d0c09b');
        $ef = ExpressionFactory::new();
        $posts = $em->getReader(Post::class)->findAll(
            Criteria::new()
                ->where($ef->property('author', $ef->same($user)))
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
    public function testReaderFindByPrimaryQueryOnce(OrmCapsule $capsule): void
    {
        $fixtureLoader = new CsvFileFixtureLoader($capsule->database());
        $fixtureLoader->load(__DIR__ . '/Fixtures/config/user.csv');
        $fixtureLoader->load(__DIR__ . '/Fixtures/config/post.csv');

        $em = new CycleEntityManager($capsule->orm());
        $user1 = $em->getReader(User::class)->findByPrimary('35a60006-c34a-4c0b-8e9d-7759f6d0c09b');
        $user2 = $em->getReader(User::class)->findByPrimary('35a60006-c34a-4c0b-8e9d-7759f6d0c09b');
        self::assertSame('35a60006-c34a-4c0b-8e9d-7759f6d0c09b', $user1->id);
        self::assertSame($user1, $user2);
        self::assertSame(1, $capsule->countReadQueries());
    }

    /**
     * @dataProvider ormCapsuleProvider
     */
    public function testReaderFindByPrimaryUsingReference(OrmCapsule $capsule): void
    {
        $fixtureLoader = new CsvFileFixtureLoader($capsule->database());
        $fixtureLoader->load(__DIR__ . '/Fixtures/config/user.csv');
        $fixtureLoader->load(__DIR__ . '/Fixtures/config/post.csv');

        $em = new CycleEntityManager($capsule->orm());
        /** @var User $user1 */
        $user1 = $em->getReader(User::class)->findByPrimary(new UserId('35a60006-c34a-4c0b-8e9d-7759f6d0c09b'));
        self::assertSame('35a60006-c34a-4c0b-8e9d-7759f6d0c09b', $user1->id);
    }

    /**
     * @dataProvider ormCapsuleProvider
     */
    public function testReaderFindAllAlwaysTrueAndAlwaysFalse(OrmCapsule $capsule): void
    {
        $fixtureLoader = new CsvFileFixtureLoader($capsule->database());
        $fixtureLoader->load(__DIR__ . '/Fixtures/config/user.csv');
        $fixtureLoader->load(__DIR__ . '/Fixtures/config/post.csv');

        $em = new CycleEntityManager($capsule->orm());
        $ef = ExpressionFactory::new();
        self::assertCount(
            $em->getReader(Post::class)->count(),
            $em->getReader(Post::class)->findAll(Criteria::new()->where($ef->true()))
        );
        self::assertCount(
            0,
            $em->getReader(Post::class)->findAll(Criteria::new()->where($ef->false()))
        );
    }

    /**
     * @dataProvider ormCapsuleProvider
     */
    public function testReadWriteEntitiesUsedPlugin(OrmCapsule $capsule): void
    {
        $fixtureLoader = new CsvFileFixtureLoader($capsule->database());
        $fixtureLoader->load(__DIR__ . '/Fixtures/config/user.csv');
        $fixtureLoader->load(__DIR__ . '/Fixtures/config/todo_item.csv');

        $em = new CycleEntityManager($capsule->orm());
        $ef = ExpressionFactory::new();

        /** @var TodoItem[] $items */
        $items = $em->getReader(TodoItem::class)->findAll(
            Criteria::new()
                ->where($ef->property('blame.createdAt', $ef->greaterThanEqual(DateTimeImmutableValue::from('2021-09-19 16:00:00'))))
                ->orderBy([
                    'blame.createdAt' => \SORT_DESC,
                ])
        );

        self::assertCount(5, $items);

        foreach ($items as $item) {
            $item->blame($item->getBlame()->getUpdatedBy(), true);
        }

        $em->save(...$items);
    }
}
