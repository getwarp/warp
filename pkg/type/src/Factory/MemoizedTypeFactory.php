<?php

declare(strict_types=1);

namespace spaceonfire\Type\Factory;

use spaceonfire\Type\TypeInterface;

final class MemoizedTypeFactory implements TypeFactoryInterface
{
    /**
     * @var TypeFactoryInterface
     */
    private $underlyingFactory;

    /**
     * @var array<string,bool>
     */
    private $cacheSupports = [];

    /**
     * @var array<string,TypeInterface>
     */
    private $cacheMake = [];

    /**
     * MemoizedTypeFactory constructor.
     * @param TypeFactoryInterface $underlyingFactory
     */
    public function __construct(TypeFactoryInterface $underlyingFactory)
    {
        $this->underlyingFactory = $underlyingFactory;
        $this->underlyingFactory->setParent($this);
    }

    /**
     * @inheritDoc
     */
    public function supports(string $type): bool
    {
        if (!isset($this->cacheSupports[$type])) {
            $this->cacheSupports[$type] = $this->underlyingFactory->supports($type);
        }

        return $this->cacheSupports[$type];
    }

    /**
     * @inheritDoc
     */
    public function make(string $type): TypeInterface
    {
        if (!isset($this->cacheMake[$type])) {
            $this->cacheMake[$type] = $this->underlyingFactory->make($type);
        }

        return $this->cacheMake[$type];
    }

    /**
     * @inheritDoc
     */
    public function setParent(TypeFactoryInterface $parent): void
    {
        $this->underlyingFactory->setParent($parent);
    }
}
