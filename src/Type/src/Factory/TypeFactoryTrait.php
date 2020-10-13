<?php

declare(strict_types=1);

namespace spaceonfire\Type\Factory;

trait TypeFactoryTrait
{
    /**
     * @var TypeFactoryInterface|null
     */
    protected $parent;

    /**
     * Setter for `parent` property
     * @param TypeFactoryInterface $parent
     */
    public function setParent(TypeFactoryInterface $parent): void
    {
        $this->parent = $parent;
    }
}
