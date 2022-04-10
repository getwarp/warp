<?php

declare(strict_types=1);

namespace Warp\Bridge\Cycle\Mapper\Plugin;

use Warp\Bridge\Cycle\Mapper\MapperPluginInterface;

final class NoopMapperPlugin implements MapperPluginInterface
{
    public function dispatch(object $event): object
    {
        return $event;
    }
}
