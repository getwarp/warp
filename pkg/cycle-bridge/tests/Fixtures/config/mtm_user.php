<?php

declare(strict_types=1);

namespace App;

use spaceonfire\Bridge\Cycle\Fixtures\FixtureUser;
use spaceonfire\Bridge\Cycle\Mapper\HydratorMapper;
use spaceonfire\Bridge\Cycle\Schema\EntityDto;
use spaceonfire\Bridge\Cycle\Schema\FieldDto;
use spaceonfire\Bridge\Cycle\Schema\RelationDto;

return [
    EntityDto::ROLE => 'mtm_user',
    EntityDto::TABLE => 'mtm_user',
    EntityDto::CLASS_NAME => FixtureUser::class,
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
            FieldDto::NAME => 'email',
            FieldDto::TYPE => 'string(255)',
            FieldDto::OPTIONS => [
                FieldDto::OPTION_NULLABLE => false,
            ],
        ],
    ],
    EntityDto::RELATIONS => [
        [
            RelationDto::NAME => 'tags',
            RelationDto::TARGET => 'mtm_tag',
            RelationDto::TYPE => RelationDto::TYPE_MANY_TO_MANY,
            RelationDto::OPTIONS => [
                RelationDto::OPTION_NULLABLE => false,
                RelationDto::OPTION_THROUGH => 'mtm_tag_user_map',
                RelationDto::OPTION_THROUGH_INNER_KEY => 'user_id',
                RelationDto::OPTION_THROUGH_OUTER_KEY => 'tag_id',
            ],
        ],
    ],
];
