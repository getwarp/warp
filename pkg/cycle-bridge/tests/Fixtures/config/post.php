<?php

declare(strict_types=1);

use Cycle\Database\Schema\AbstractColumn;
use Warp\Bridge\Cycle\Fixtures\Mapper\PostMapper;
use Warp\Bridge\Cycle\Fixtures\Post;
use Warp\Bridge\Cycle\Schema\EntityDto;
use Warp\Bridge\Cycle\Schema\FieldDto;
use Warp\Bridge\Cycle\Schema\RelationDto;

return [
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
];
