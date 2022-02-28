<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Fixtures;

use Cycle\Database\DatabaseInterface;
use Cycle\Database\DatabaseManager;
use Cycle\Database\DatabaseProviderInterface;
use Cycle\ORM\ORM;

final class OrmCapsule implements DatabaseProviderInterface
{
    private DatabaseManager $dbal;

    private ORM $orm;

    private TestLogger $logger;

    public function __construct(DatabaseManager $dbal, ORM $orm)
    {
        $this->dbal = $dbal;
        $this->orm = $orm;
        $this->logger = new TestLogger();

        $this->dbal->setLogger($this->logger);
    }

    public function database(string $database = null): DatabaseInterface
    {
        return $this->dbal->database($database);
    }

    public function dbal(): DatabaseManager
    {
        return $this->dbal;
    }

    public function orm(): ORM
    {
        return $this->orm;
    }

    public function countWriteQueries(): int
    {
        return $this->logger->countWriteQueries();
    }

    public function countReadQueries(): int
    {
        return $this->logger->countReadQueries();
    }

    public function display(): void
    {
        $this->logger->display();
    }

    public function hide(): void
    {
        $this->logger->hide();
    }

//    private function importFixtureData(): void
//    {
//        $fixtures = [
//            CycleUserRepository::class => [
//                1 => new User('35a60006-c34a-4c0b-8e9d-7759f6d0c09b', 'Admin User'),
//            ],
//            CyclePostRepository::class => [
//                1 => new Post(
//                    '0de0e289-28a7-4944-953a-079d09ac4865',
//                    'Hello, World!',
//                    '35a60006-c34a-4c0b-8e9d-7759f6d0c09b'
//                ),
//                2 => new Post(
//                    'be1cc1cc-0389-49c5-bb60-1368141b48b3',
//                    'New Post',
//                    '35a60006-c34a-4c0b-8e9d-7759f6d0c09b'
//                ),
//            ],
//            CycleTagRepository::class => [
//                $this->orm->make('tag', [
//                    'id' => 24,
//                ]),
//                $this->orm->make('tag', [
//                    'id' => 42,
//                ]),
//            ]
//        ];
//
//        foreach ($fixtures as $repoClass => $entities) {
//            $repository = $this->getRepository($repoClass);
//
//            foreach ($entities as $entity) {
//                $repository->save($entity);
//            }
//        }
//    }
//
//    /**
//     * @param Database\Database|null $database
//     */
//    private static function dropDatabase(?Database\Database $database = null): void
//    {
//        if ($database === null) {
//            return;
//        }
//
//        foreach ($database->getTables() as $table) {
//            $schema = $table->getSchema();
//
//            foreach ($schema->getForeignKeys() as $foreign) {
//                $schema->dropForeignKey($foreign->getColumns());
//            }
//
//            $schema->save(HandlerInterface::DROP_FOREIGN_KEYS);
//        }
//
//        foreach ($database->getTables() as $table) {
//            $schema = $table->getSchema();
//            $schema->declareDropped();
//            $schema->save();
//        }
//    }
}
