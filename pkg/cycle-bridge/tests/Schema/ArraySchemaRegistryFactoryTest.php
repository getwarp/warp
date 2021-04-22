<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Schema;

use Cycle\ORM\Relation;
use Cycle\ORM\SchemaInterface;
use Cycle\ORM\Select\Repository;
use Cycle\ORM\Select\Source;
use Cycle\Schema\Generator;
use spaceonfire\Bridge\Cycle\AbstractTestCase;
use spaceonfire\Bridge\Cycle\Fixtures\Mapper\PostMapper;
use spaceonfire\Bridge\Cycle\Fixtures\Mapper\UserMapper;
use spaceonfire\Bridge\Cycle\Fixtures\OrmCapsule;
use spaceonfire\Bridge\Cycle\Fixtures\Post;
use spaceonfire\Bridge\Cycle\Fixtures\User;
use spaceonfire\Bridge\Cycle\Mapper\StdClassMapper;
use Spiral\Database\Schema\AbstractColumn;

class ArraySchemaRegistryFactoryTest extends AbstractTestCase
{
    /**
     * @dataProvider ormCapsuleProvider
     */
    public function testCompile(OrmCapsule $capsule): void
    {
        $factory = new ArraySchemaRegistryFactory($capsule->dbal());
        $factory->addGenerator(
            new Generator\GenerateRelations(),
            new Generator\GenerateTypecast(),
        );
        $factory->loadEntity(
            [
                EntityDto::ROLE => 'post',
                EntityDto::TABLE => 'post',
                EntityDto::CLASS_NAME => Post::class,
                EntityDto::MAPPER => PostMapper::class,
                EntityDto::FIELDS => [
                    [
                        FieldDto::NAME => 'id',
                        FieldDto::COLUMN => 'id',
                        FieldDto::TYPE => 'uuid',
                        FieldDto::PRIMARY => true,
                        FieldDto::OPTIONS => [
                            FieldDto::OPTION_NULLABLE => false,
                        ],
                    ],
                    [
                        FieldDto::NAME => 'title',
                        FieldDto::COLUMN => 'title',
                        FieldDto::TYPE => 'string(255)',
                        FieldDto::OPTIONS => [
                            FieldDto::OPTION_NULLABLE => false,
                        ],
                    ],
                    [
                        FieldDto::NAME => 'authorId',
                        FieldDto::COLUMN => 'author_id',
                        FieldDto::TYPE => 'uuid',
                        FieldDto::OPTIONS => [
                            FieldDto::OPTION_NULLABLE => false,
                        ],
                    ],
                    [
                        FieldDto::NAME => 'createdAt',
                        FieldDto::COLUMN => 'created_at',
                        FieldDto::TYPE => 'datetime',
                        FieldDto::OPTIONS => [
                            FieldDto::OPTION_NULLABLE => false,
                            FieldDto::OPTION_DEFAULT => AbstractColumn::DATETIME_NOW,
                        ],
                    ],
                ],
                EntityDto::RELATIONS => [
                    [
                        RelationDto::NAME => 'author',
                        RelationDto::TARGET => 'user',
                        RelationDto::TYPE => RelationDto::TYPE_BELONGS_TO,
                        RelationDto::OPTIONS => [
                            RelationDto::OPTION_NULLABLE => false,
                            RelationDto::OPTION_INNER_KEY => 'authorId',
                            RelationDto::OPTION_FK_CREATE => true,
                        ],
                    ],
                ],
            ],
            [
                EntityDto::ROLE => 'user',
                EntityDto::TABLE => 'user',
                EntityDto::CLASS_NAME => User::class,
                EntityDto::MAPPER => UserMapper::class,
                EntityDto::FIELDS => [
                    [
                        FieldDto::NAME => 'id',
                        FieldDto::COLUMN => 'id',
                        FieldDto::TYPE => 'uuid',
                        FieldDto::PRIMARY => true,
                        FieldDto::OPTIONS => [
                            FieldDto::OPTION_NULLABLE => false,
                        ],
                    ],
                    [
                        FieldDto::NAME => 'name',
                        FieldDto::COLUMN => 'name',
                        FieldDto::TYPE => 'string(255)',
                        FieldDto::OPTIONS => [
                            FieldDto::OPTION_NULLABLE => false,
                        ],
                    ],
                ],
            ],
            [
                EntityDto::ROLE => 'tag',
                EntityDto::TABLE => 'tag',
                EntityDto::FIELDS => [
                    [
                        FieldDto::NAME => 'id',
                        FieldDto::TYPE => 'primary',
                        FieldDto::PRIMARY => true,
                        FieldDto::OPTIONS => [
                            FieldDto::OPTION_NULLABLE => false,
                        ],
                    ],
                ],
            ],
        );

        $schema = $factory->compile();

        self::assertSame([
            'post' => [
                SchemaInterface::ENTITY => Post::class,
                SchemaInterface::MAPPER => PostMapper::class,
                SchemaInterface::SOURCE => Source::class,
                SchemaInterface::REPOSITORY => Repository::class,
                SchemaInterface::DATABASE => 'default',
                SchemaInterface::TABLE => 'post',
                SchemaInterface::PRIMARY_KEY => 'id',
                SchemaInterface::FIND_BY_KEYS => ['id'],
                SchemaInterface::COLUMNS => [
                    'id' => 'id',
                    'title' => 'title',
                    'authorId' => 'author_id',
                    'createdAt' => 'created_at',
                ],
                SchemaInterface::RELATIONS => [
                    'author' => [
                        Relation::TYPE => Relation::BELONGS_TO,
                        Relation::TARGET => 'user',
                        Relation::LOAD => Relation::LOAD_PROMISE,
                        Relation::SCHEMA => [
                            Relation::CASCADE => true,
                            Relation::NULLABLE => false,
                            Relation::INNER_KEY => 'authorId',
                            Relation::OUTER_KEY => 'id',
                        ],
                    ],
                ],
                SchemaInterface::SCOPE => null,
                SchemaInterface::TYPECAST => [
                    'createdAt' => 'datetime',
                ],
                SchemaInterface::SCHEMA => [],
            ],
            'user' => [
                SchemaInterface::ENTITY => User::class,
                SchemaInterface::MAPPER => UserMapper::class,
                SchemaInterface::SOURCE => Source::class,
                SchemaInterface::REPOSITORY => Repository::class,
                SchemaInterface::DATABASE => 'default',
                SchemaInterface::TABLE => 'user',
                SchemaInterface::PRIMARY_KEY => 'id',
                SchemaInterface::FIND_BY_KEYS => [
                    'id',
                ],
                SchemaInterface::COLUMNS => [
                    'id' => 'id',
                    'name' => 'name',
                ],
                SchemaInterface::RELATIONS => [],
                SchemaInterface::SCOPE => null,
                SchemaInterface::TYPECAST => [],
                SchemaInterface::SCHEMA => [],
            ],
            'tag' => [
                SchemaInterface::ENTITY => null,
                SchemaInterface::MAPPER => StdClassMapper::class,
                SchemaInterface::SOURCE => Source::class,
                SchemaInterface::REPOSITORY => Repository::class,
                SchemaInterface::DATABASE => 'default',
                SchemaInterface::TABLE => 'tag',
                SchemaInterface::PRIMARY_KEY => 'id',
                SchemaInterface::FIND_BY_KEYS => [
                    'id',
                ],
                SchemaInterface::COLUMNS => [
                    'id' => 'id',
                ],
                SchemaInterface::RELATIONS => [],
                SchemaInterface::SCOPE => null,
                SchemaInterface::TYPECAST => [
                    'id' => 'int',
                ],
                SchemaInterface::SCHEMA => [],
            ],
        ], $schema);
    }
}
