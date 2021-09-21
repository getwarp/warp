<?php

declare(strict_types=1);

namespace spaceonfire\Container;

use function class_alias;

class_alias(CompositeContainer::class, __NAMESPACE__ . '\ContainerChain');

if (false) {
    /**
     * @deprecated Will be dropped in next major release.
     * Use \spaceonfire\Container\CompositeContainer instead.
     */
    class ContainerChain extends CompositeContainer
    {
    }
}
