<?php

declare(strict_types=1);

namespace Warp\Container\Fixtures\AbstractClass;

final class RequiresAbstractClass
{
    /**
     * @var AbstractClass
     */
    private $abstractClass;

    public function __construct(AbstractClass $abstractClass)
    {
        $this->abstractClass = $abstractClass;
    }

    /**
     * Getter for `abstractClass` property.
     * @return AbstractClass
     */
    public function getAbstractClass(): AbstractClass
    {
        return $this->abstractClass;
    }
}
