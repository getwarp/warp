<?php

declare(strict_types=1);

namespace Warp\Container\Argument;

use Warp\Container\RawValueHolder;
use function class_alias;

class_alias(RawValueHolder::class, __NAMESPACE__ . '\ArgumentValue');

if (false) {
    /**
     * @deprecated Will be dropped in next major release.
     * Use \Warp\Container\RawValueHolder instead.
     */
    final class ArgumentValue extends RawValueHolder
    {
    }
}
