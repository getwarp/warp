<?php

declare(strict_types=1);

namespace spaceonfire\CommandBus;

use Throwable;

/**
 * Marker interface for all CommandBus exceptions
 */
interface Exception extends Throwable
{
}
