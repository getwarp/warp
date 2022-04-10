<?php

declare(strict_types=1);

namespace Warp\Container;

/**
 * @template T
 * @extends FactoryInterface<T>
 */
interface DefinitionInterface extends FactoryInterface, FactoryOptionsInterface, ContainerAwareInterface
{
    public function getId(): string;

    public function hasTag(string $tag): bool;

    /**
     * @return string[]
     */
    public function getTags(): array;

    /**
     * @param string $tag
     * @return $this
     */
    public function addTag(string $tag): self;
}
