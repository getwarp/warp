<?php

declare(strict_types=1);

namespace Warp\Bridge\Cycle\Fixtures;

use Warp\Bridge\Cycle\Collection\ObjectCollectionInterface;

final class FixtureUser
{
    /** @var int|null */
    public $id;

    /** @var string */
    public $email;

    /** @var ObjectCollectionInterface<FixtureTag,object> */
    public $tags;
}
