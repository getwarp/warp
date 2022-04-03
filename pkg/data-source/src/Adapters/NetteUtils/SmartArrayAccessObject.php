<?php

declare(strict_types=1);

namespace Warp\DataSource\Adapters\NetteUtils;

use function class_alias;

class_alias(
    \Warp\DataSource\Bridge\NetteUtils\SmartArrayAccessObject::class,
    __NAMESPACE__ . '\SmartArrayAccessObject'
);

if (false) {
    /**
     * @deprecated Will be dropped in next major release.
     * Use \Warp\DataSource\Bridge\NetteUtils\SmartArrayAccessObject instead.
     */
    trait SmartArrayAccessObject
    {
        use \Warp\DataSource\Bridge\NetteUtils\SmartArrayAccessObject;
    }
}
