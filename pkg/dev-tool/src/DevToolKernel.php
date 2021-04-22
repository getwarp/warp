<?php

declare(strict_types=1);

namespace spaceonfire\DevTool;

use spaceonfire\Common\Kernel\AbstractKernel;
use spaceonfire\Common\Kernel\ConsoleApplicationConfiguratorTrait;

final class DevToolKernel extends AbstractKernel
{
    use ConsoleApplicationConfiguratorTrait;

    protected function loadServiceProviders(): iterable
    {
        yield DI\CommandsProvider::class;
    }
}
