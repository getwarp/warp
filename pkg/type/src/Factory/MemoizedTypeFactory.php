<?php

declare(strict_types=1);

namespace spaceonfire\Type\Factory;

use spaceonfire\Type\TypeInterface;

final class MemoizedTypeFactory implements TypeFactoryInterface
{
    private TypeFactoryInterface $innerFactory;

    /**
     * @var array<string,bool>
     */
    private array $cacheSupports = [];

    /**
     * @var array<string,TypeInterface>
     */
    private array $cacheMake = [];

    public function __construct(TypeFactoryInterface $innerFactory)
    {
        $this->innerFactory = $innerFactory;
        $this->innerFactory->setParent($this);
    }

    public function supports(string $type): bool
    {
        if (!isset($this->cacheSupports[$type])) {
            $this->cacheSupports[$type] = $this->innerFactory->supports($type);
        }

        return $this->cacheSupports[$type];
    }

    public function make(string $type): TypeInterface
    {
        if (!isset($this->cacheMake[$type])) {
            $this->cacheMake[$type] = $this->innerFactory->make($type);
        }

        return $this->cacheMake[$type];
    }

    public function setParent(TypeFactoryInterface $parent): void
    {
        $this->innerFactory->setParent($parent);
    }
}
