<?php

declare(strict_types=1);

namespace Warp\CommandBus;

use Throwable;

/**
 * Marker interface for all CommandBus exceptions
 */
interface ExceptionInterface extends Throwable
{
}
