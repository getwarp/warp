<?php

declare(strict_types=1);

namespace spaceonfire\DataSource\Bridge\CycleOrm;

use Cycle\ORM;
use Cycle\Schema;
use Cycle\Schema\Generator;
use InvalidArgumentException;
use spaceonfire\DataSource\Bridge\CycleOrm\Fixtures\TestLogger;
use spaceonfire\DataSource\Bridge\CycleOrm\Repository\AbstractCycleRepository;
use spaceonfire\DataSource\Fixtures\Domain\Post\Post;
use spaceonfire\DataSource\Fixtures\Domain\User\User;
use spaceonfire\DataSource\Fixtures\Infrastructure\Persistence\Post\CyclePostRepository;
use spaceonfire\DataSource\Fixtures\Infrastructure\Persistence\Tag\CycleTagRepository;
use spaceonfire\DataSource\Fixtures\Infrastructure\Persistence\User\CycleUserRepository;
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

    /** @var AbstractCycleRepository[] */
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

    public function getRepository(string $className): AbstractCycleRepository
    {
        $repository = &$this->repositoriesCache[$className];

        if ($repository) {
            return $repository;
        }

        if (!isset($this->repositories[$className])) {
            throw new InvalidArgumentException(sprintf('Unknown repository class "%s"', $className));
        }

        $repository = new $className($this->orm->getRepository($this->repositories[$className]), $this->orm);

        return $repository;
    }

    public function initialize(): void
    {
        if ($this->initialized) {
            return;
        }

        $this->repositories = [CyclePostRepository::class, CycleUserRepository::class, CycleTagRepository::class];

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

        $this->orm = (new ORM\ORM(new ORM\Factory($this->dbal), $this->buildSchema($this->dbal)))
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

    private function buildSchema(Database\DatabaseProviderInterface $dbal, ?string $database = null): ORM\Schema
    {
        $registry = new Schema\Registry($dbal);

        $this->repositories = array_flip($this->repositories);

        /**
         * @var AbstractCycleRepository $repository
         */
        foreach ($this->repositories as $repository => &$role) {
            $registry->register($e = $repository::define());
            $registry->linkTable($e, $database, $repository::getTableName());
            $role = $e->getRole();
        }
        unset($role);

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
