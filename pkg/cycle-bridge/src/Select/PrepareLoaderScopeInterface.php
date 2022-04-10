<?php

declare(strict_types=1);

namespace Warp\Bridge\Cycle\Select;

use Cycle\ORM\Select\AbstractLoader;

interface PrepareLoaderScopeInterface
{
    public function prepareLoader(AbstractLoader $loader): void;
}
