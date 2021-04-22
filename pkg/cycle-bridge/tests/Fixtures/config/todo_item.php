<?php

declare(strict_types=1);

use spaceonfire\Bridge\Cycle\Fixtures\Mapper\TodoItemMapper;
use spaceonfire\Bridge\Cycle\Fixtures\Todo\TodoItem;
use spaceonfire\Bridge\Cycle\Fixtures\Todo\TodoItemId;
use spaceonfire\Bridge\Cycle\Schema\EntityDto;
use spaceonfire\Bridge\Cycle\Schema\FieldDto;
use spaceonfire\Bridge\Cycle\Schema\RelationDto;
use Spiral\Database\Schema\AbstractColumn;

return [
    EntityDto::ROLE => TodoItemId::ROLE,
    EntityDto::TABLE => 'todo_item',
    EntityDto::CLASS_NAME => TodoItem::class,
    EntityDto::MAPPER => TodoItemMapper::class,
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
            FieldDto::NAME => 'content',
            FieldDto::COLUMN => 'content',
            FieldDto::TYPE => 'text',
            FieldDto::OPTIONS => [
                FieldDto::OPTION_NULLABLE => false,
            ],
        ],
        [
            FieldDto::NAME => 'done',
            FieldDto::COLUMN => 'is_done',
            FieldDto::TYPE => 'boolean',
            FieldDto::OPTIONS => [
                FieldDto::OPTION_NULLABLE => false,
                FieldDto::OPTION_DEFAULT => false,
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
        [
            FieldDto::NAME => 'createdById',
            FieldDto::COLUMN => 'created_by',
            FieldDto::TYPE => 'uuid',
            FieldDto::OPTIONS => [
                FieldDto::OPTION_NULLABLE => true,
                FieldDto::OPTION_DEFAULT => null,
            ],
        ],
        [
            FieldDto::NAME => 'updatedAt',
            FieldDto::COLUMN => 'updated_at',
            FieldDto::TYPE => 'datetime',
            FieldDto::OPTIONS => [
                FieldDto::OPTION_NULLABLE => false,
                FieldDto::OPTION_DEFAULT => AbstractColumn::DATETIME_NOW,
            ],
        ],
        [
            FieldDto::NAME => 'updatedById',
            FieldDto::COLUMN => 'updated_by',
            FieldDto::TYPE => 'uuid',
            FieldDto::OPTIONS => [
                FieldDto::OPTION_NULLABLE => true,
                FieldDto::OPTION_DEFAULT => null,
            ],
        ],
    ],
    EntityDto::RELATIONS => [
        [
            RelationDto::NAME => 'createdBy',
            RelationDto::TARGET => 'user',
            RelationDto::TYPE => RelationDto::TYPE_REFERS_TO,
            RelationDto::OPTIONS => [
                RelationDto::OPTION_NULLABLE => true,
                RelationDto::OPTION_INNER_KEY => 'createdById',
            ],
        ],
        [
            RelationDto::NAME => 'updatedBy',
            RelationDto::TARGET => 'user',
            RelationDto::TYPE => RelationDto::TYPE_REFERS_TO,
            RelationDto::OPTIONS => [
                RelationDto::OPTION_NULLABLE => true,
                RelationDto::OPTION_INNER_KEY => 'updatedById',
            ],
        ],
    ],
];
