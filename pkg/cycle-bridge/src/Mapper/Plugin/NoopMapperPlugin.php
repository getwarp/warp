<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Mapper\Plugin;

use spaceonfire\Bridge\Cycle\Mapper\MapperPluginInterface;

final class NoopMapperPlugin implements MapperPluginInterface
{
    public function dispatch(object $event): object
    {
        return $event;
    }
}
