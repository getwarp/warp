<?php

declare(strict_types=1);

namespace Warp\Container;

use function class_alias;

class_alias(CompositeContainer::class, __NAMESPACE__ . '\ContainerChain');

if (false) {
    /**
     * @deprecated Will be dropped in next major release.
     * Use \Warp\Container\CompositeContainer instead.
     */
    class ContainerChain extends CompositeContainer
    {
    }
}
