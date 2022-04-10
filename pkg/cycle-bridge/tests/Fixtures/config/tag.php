<?php

declare(strict_types=1);

use Warp\Bridge\Cycle\Schema\EntityDto;
use Warp\Bridge\Cycle\Schema\FieldDto;

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
