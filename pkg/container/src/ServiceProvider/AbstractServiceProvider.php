<?php

declare(strict_types=1);

namespace Warp\Container\ServiceProvider;

use Warp\Container\ContainerAwareTrait;

abstract class AbstractServiceProvider implements ServiceProviderInterface
{
    use ContainerAwareTrait;

    /**
     * @var string|null
     */
    protected $identifier;

    /**
     * @inheritDoc
     */
    public function setIdentifier(string $id): ServiceProviderInterface
    {
        $this->identifier = $id;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getIdentifier(): string
    {
        return $this->identifier ?? static::class;
    }
}
