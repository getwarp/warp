<?php

declare(strict_types=1);

namespace spaceonfire\Type\Factory;

use spaceonfire\Type\TypeInterface;

final class MemoizedTypeFactory implements TypeFactoryInterface
{
    private TypeFactoryInterface $underlyingFactory;

    /**
     * @var array<string,bool>
     */
    private array $cacheSupports = [];

    /**
     * @var array<string,TypeInterface>
     */
    private array $cacheMake = [];

    public function __construct(TypeFactoryInterface $underlyingFactory)
    {
        $this->underlyingFactory = $underlyingFactory;
        $this->underlyingFactory->setParent($this);
    }

    public function supports(string $type): bool
    {
        if (!isset($this->cacheSupports[$type])) {
            $this->cacheSupports[$type] = $this->underlyingFactory->supports($type);
        }

        return $this->cacheSupports[$type];
    }

    public function make(string $type): TypeInterface
    {
        if (!isset($this->cacheMake[$type])) {
            $this->cacheMake[$type] = $this->underlyingFactory->make($type);
        }

        return $this->cacheMake[$type];
    }

    public function setParent(TypeFactoryInterface $parent): void
    {
        $this->underlyingFactory->setParent($parent);
    }
}
