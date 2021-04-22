<?php

declare(strict_types=1);

use spaceonfire\Bridge\Cycle\Schema\EntityDto;
use spaceonfire\Bridge\Cycle\Schema\FieldDto;

return [
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
];
