<?php

declare(strict_types=1);

use spaceonfire\Bridge\Cycle\Fixtures\Mapper\UserMapper;
use spaceonfire\Bridge\Cycle\Fixtures\User;
use spaceonfire\Bridge\Cycle\Schema\EntityDto;
use spaceonfire\Bridge\Cycle\Schema\FieldDto;

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
