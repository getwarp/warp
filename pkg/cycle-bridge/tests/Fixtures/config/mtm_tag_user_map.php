<?php

declare(strict_types=1);

namespace App;

use spaceonfire\Bridge\Cycle\Mapper\StdClassMapper;
use spaceonfire\Bridge\Cycle\Schema\EntityDto;
use spaceonfire\Bridge\Cycle\Schema\FieldDto;

return [
    EntityDto::ROLE => 'mtm_tag_user_map',
    EntityDto::TABLE => 'mtm_tag_user_map',
    EntityDto::MAPPER => StdClassMapper::class,
    EntityDto::FIELDS => [
        [
            FieldDto::NAME => 'id',
            FieldDto::TYPE => 'primary',
            FieldDto::PRIMARY => true,
            FieldDto::OPTIONS => [
                FieldDto::OPTION_NULLABLE => false,
            ],
        ],
        [
            FieldDto::NAME => 'user_id',
            FieldDto::TYPE => 'integer',
            FieldDto::OPTIONS => [
                FieldDto::OPTION_NULLABLE => false,
            ],
        ],
        [
            FieldDto::NAME => 'tag_id',
            FieldDto::TYPE => 'integer',
            FieldDto::OPTIONS => [
                FieldDto::OPTION_NULLABLE => false,
            ],
        ],
    ],
];
