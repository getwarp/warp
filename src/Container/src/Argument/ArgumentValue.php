<?php

declare(strict_types=1);

namespace spaceonfire\Container\Argument;

use spaceonfire\Container\RawValueHolder;
use function class_alias;

class_alias(RawValueHolder::class, __NAMESPACE__ . '\ArgumentValue');

if (false) {
    /**
     * @deprecated Will be dropped in next major release.
     * Use \spaceonfire\Container\RawValueHolder instead.
     */
    final class ArgumentValue extends RawValueHolder
    {
    }
}
