<?php

declare(strict_types=1);

namespace App;

use spaceonfire\Bridge\Cycle\Fixtures\FixtureTag;
use spaceonfire\Bridge\Cycle\Mapper\HydratorMapper;
use spaceonfire\Bridge\Cycle\Schema\EntityDto;
use spaceonfire\Bridge\Cycle\Schema\FieldDto;

return [
    EntityDto::ROLE => 'mtm_tag',
    EntityDto::TABLE => 'mtm_tag',
    EntityDto::CLASS_NAME => FixtureTag::class,
    EntityDto::MAPPER => HydratorMapper::class,
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
            FieldDto::NAME => 'tag',
            FieldDto::TYPE => 'string(255)',
            FieldDto::OPTIONS => [
                FieldDto::OPTION_NULLABLE => false,
            ],
        ],
    ],
];
