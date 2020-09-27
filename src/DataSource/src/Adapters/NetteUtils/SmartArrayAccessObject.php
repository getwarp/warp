<?php

declare(strict_types=1);

namespace spaceonfire\DataSource\Adapters\NetteUtils;

use function class_alias;

class_alias(
    \spaceonfire\DataSource\Bridge\NetteUtils\SmartArrayAccessObject::class,
    __NAMESPACE__ . '\SmartArrayAccessObject'
);

if (false) {
    /**
     * @deprecated Will be dropped in next major release.
     * Use \spaceonfire\DataSource\Bridge\NetteUtils\SmartArrayAccessObject instead.
     */
    trait SmartArrayAccessObject
    {
        use \spaceonfire\DataSource\Bridge\NetteUtils\SmartArrayAccessObject;
    }
}
