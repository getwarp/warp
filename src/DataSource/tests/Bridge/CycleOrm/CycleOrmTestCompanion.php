<?php

declare(strict_types=1);

namespace spaceonfire\DataSource\Bridge\CycleOrm;

use Cycle\ORM;
use Cycle\Schema;
use Cycle\Schema\Definition\Entity;
use Cycle\Schema\Definition\Field;
use Cycle\Schema\Definition\Relation;
use Cycle\Schema\Generator;
use Cycle\Schema\Registry;
use InvalidArgumentException;
use spaceonfire\DataSource\Bridge\CycleOrm\Fixtures\TestLogger;
use spaceonfire\DataSource\Bridge\CycleOrm\Mapper\UuidCycleMapper;
use spaceonfire\DataSource\Bridge\CycleOrm\Schema\AbstractRegistryFactory;
use spaceonfire\DataSource\Fixtures\Domain\Post\Post;
use spaceonfire\DataSource\Fixtures\Domain\User\User;
use spaceonfire\DataSource\Fixtures\Infrastructure\Persistence\Post\CyclePostRepository;
use spaceonfire\DataSource\Fixtures\Infrastructure\Persistence\Tag\CycleTagRepository;
use spaceonfire\DataSource\Fixtures\Infrastructure\Persistence\User\CycleUserRepository;
use spaceonfire\DataSource\RepositoryInterface;
use Spiral\Database;
use Spiral\Database\Config\DatabaseConfig;
use Spiral\Database\DatabaseManager;
use Spiral\Database\Driver\HandlerInterface;
use Spiral\Database\Driver\SQLite\SQLiteDriver;
use Webmozart\Assert\Assert;

class CycleOrmTestCompanion
{
    /** @var DatabaseManager */
    private $dbal;

    /** @var ORM\ORM */
    private $orm;

    /** @var string[] */
    private $repositories;

    /** @var RepositoryInterface[] */
    private $repositoriesCache;

    /** @var bool */
    private $initialized = false;
    /**
     * @var TestLogger
     */
    private $logger;

    public function __construct()
    {
        $this->initialize();
    }

    /**
     * Getter for `orm` property
     * @return ORM\ORM
     */
    public function getOrm(): ORM\ORM
    {
        return $this->orm;
    }

    /**
     * Getter for `dbal` property
     * @return DatabaseManager
     */
    public function getDbal(): DatabaseManager
    {
        return $this->dbal;
    }

    /**
     * Returns test driver
     * @return Database\Driver\DriverInterface|Database\Driver\Driver
     */
    public function getDriver(): Database\Driver\DriverInterface
    {
        Assert::notNull($this->dbal);
        return $this->dbal->driver('sqlite');
    }

    /**
     * Getter for `logger` property
     * @return TestLogger
     */
    public function getLogger(): TestLogger
    {
        return $this->logger;
    }

    public function getRepository(string $className): RepositoryInterface
    {
        $repository = &$this->repositoriesCache[$className];

        if ($repository) {
            return $repository;
        }

        if (!isset($this->repositories[$className])) {
            throw new InvalidArgumentException(sprintf('Unknown repository class "%s"', $className));
        }

        $repository = new $className($this->orm);

        return $repository;
    }

    public function initialize(): void
    {
        if ($this->initialized) {
            return;
        }

        $this->repositories = [
            CyclePostRepository::class => 'post',
            CycleUserRepository::class => 'user',
            CycleTagRepository::class => 'tag',
        ];

        $this->repositoriesCache = [];

        $this->dbal = new DatabaseManager(new DatabaseConfig([
            'default' => 'default',
            'databases' => [
                'default' => ['connection' => 'sqlite'],
            ],
            'connections' => [
                'sqlite' => [
                    'driver' => SQLiteDriver::class,
                    'options' => [
                        'connection' => 'sqlite::memory:',
                        'username' => '',
                        'password' => '',
                    ],
                ],
            ],
        ]));

        $this->logger = new TestLogger();
//        $this->logger->display();
        $this->getDriver()->setLogger($this->logger);

        $this->orm = (new ORM\ORM(new ORM\Factory($this->dbal), $this->buildSchema()))
            ->withPromiseFactory(new ORM\Promise\PromiseFactory());

        $this->importFixtureData();

        $this->initialized = true;
    }

    public function tearDown(): void
    {
        self::dropDatabase($this->dbal->database('default'));
        $this->repositoriesCache = null;
        $this->repositories = null;
        $this->orm = null;
        $this->dbal = null;
        $this->initialized = false;

        if (function_exists('gc_collect_cycles')) {
            gc_collect_cycles();
        }
    }

    private function buildSchema(): ORM\Schema
    {
        $registryFactory = new class($this->dbal) extends AbstractRegistryFactory {
            private $dbal;

            public function __construct($dbal)
            {
                $this->dbal = $dbal;
            }

            public function make(): Registry
            {
                $registry = new Registry($this->dbal);

                $post = new Entity();
                $post->setRole('post');
                $post->setClass(Post::class);

                $post->getFields()->set('id', (new Field())->setType('string(36)')->setColumn('id')->setPrimary(true));
                $post->getFields()->set('title', (new Field())->setType('string(255)')->setColumn('title'));
                $post->getFields()->set('authorId', (new Field())->setType('string(36)')->setColumn('authorId'));

                $post->getRelations()->set('author', (new Relation())->setTarget(User::class)->setType('belongsTo'));
                $post->getRelations()->get('author')->getOptions()->set('innerKey', 'authorId');

                $post->setMapper(UuidCycleMapper::class);

                $this->autocompleteEntity($post);
                $registry->register($post);
                $registry->linkTable($post, null, 'posts');

                $user = new Entity();
                $user->setRole('user');
                $user->setClass(User::class);

                $user->getFields()->set('id', (new Field())->setType('string(36)')->setColumn('id')->setPrimary(true));
                $user->getFields()->set('name', (new Field())->setType('string(255)')->setColumn('name'));

                $user->setMapper(UuidCycleMapper::class);

                $this->autocompleteEntity($user);
                $registry->register($user);
                $registry->linkTable($user, null, 'users');

                $tag = new Entity();
                $tag->setRole('tag');

                $tag->getFields()->set('id', (new Field())->setType('primary')->setColumn('id')->setPrimary(true));

                $this->autocompleteEntity($tag);
                $registry->register($tag);
                $registry->linkTable($tag, null, 'tags');

                return $registry;
            }
        };

        $registry = $registryFactory->make();

        $schema = (new Schema\Compiler())->compile($registry, [
            new Generator\ResetTables(),
            new Generator\GenerateRelations(),
            new Generator\ValidateEntities(),
            new Generator\RenderTables(),
            new Generator\RenderRelations(),
            new Generator\SyncTables(),
            new Generator\GenerateTypecast(),
        ]);

        return new ORM\Schema($schema);
    }

    private function importFixtureData(): void
    {
        $fixtures = [
            CycleUserRepository::class => [
                1 => new User('35a60006-c34a-4c0b-8e9d-7759f6d0c09b', 'Admin User'),
            ],
            CyclePostRepository::class => [
                1 => new Post(
                    '0de0e289-28a7-4944-953a-079d09ac4865',
                    'Hello, World!',
                    '35a60006-c34a-4c0b-8e9d-7759f6d0c09b'
                ),
                2 => new Post(
                    'be1cc1cc-0389-49c5-bb60-1368141b48b3',
                    'New Post',
                    '35a60006-c34a-4c0b-8e9d-7759f6d0c09b'
                ),
            ],
            CycleTagRepository::class => [
                $this->orm->make('tag', [
                    'id' => 24,
                ]),
                $this->orm->make('tag', [
                    'id' => 42,
                ]),
            ]
        ];

        foreach ($fixtures as $repoClass => $entities) {
            $repository = $this->getRepository($repoClass);

            foreach ($entities as $entity) {
                $repository->save($entity);
            }
        }
    }

    /**
     * @param Database\Database|null $database
     */
    private static function dropDatabase(?Database\Database $database = null): void
    {
        if ($database === null) {
            return;
        }

        foreach ($database->getTables() as $table) {
            $schema = $table->getSchema();

            foreach ($schema->getForeignKeys() as $foreign) {
                $schema->dropForeignKey($foreign->getColumns());
            }

            $schema->save(HandlerInterface::DROP_FOREIGN_KEYS);
        }

        foreach ($database->getTables() as $table) {
            $schema = $table->getSchema();
            $schema->declareDropped();
            $schema->save();
        }
    }
}
