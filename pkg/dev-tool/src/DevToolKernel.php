<?php

declare(strict_types=1);

namespace Warp\DevTool;

use Warp\Common\Kernel\AbstractKernel;
use Warp\Common\Kernel\ConsoleApplicationConfiguratorTrait;

final class DevToolKernel extends AbstractKernel
{
    use ConsoleApplicationConfiguratorTrait;

    protected function loadServiceProviders(): iterable
    {
        yield DI\CommandsProvider::class;
    }
}
