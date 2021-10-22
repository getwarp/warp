<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Fixtures;

use spaceonfire\Bridge\Cycle\Collection\ObjectCollectionInterface;

final class FixtureUser
{
    /** @var int|null */
    public $id;

    /** @var string */
    public $email;

    /** @var ObjectCollectionInterface<FixtureTag,object> */
    public $tags;
}
