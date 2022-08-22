<?php

declare(strict_types=1);

namespace Vendor;

use Warp\DataSource\EntityEventsTrait;

final class ExampleEventsEntity
{
    use EntityEventsTrait;

    public function __construct()
    {
        $this->recordEvent(new FooEvent());
    }
}
