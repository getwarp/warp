<?php

declare(strict_types=1);

use Warp\Bridge\Cycle\Fixtures\Mapper\UserMapper;
use Warp\Bridge\Cycle\Fixtures\User;
use Warp\Bridge\Cycle\Schema\EntityDto;
use Warp\Bridge\Cycle\Schema\FieldDto;

return [
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
];
