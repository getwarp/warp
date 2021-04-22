<?php

declare(strict_types=1);

namespace spaceonfire\Type\Factory;

use spaceonfire\Type\Exception\TypeNotSupportedException;
use spaceonfire\Type\InstanceOfType;
use spaceonfire\Type\TypeInterface;

final class InstanceOfTypeFactory implements TypeFactoryInterface
{
    use TypeFactoryTrait;

    private bool $autoload;

    public function __construct(bool $autoload = true)
    {
        $this->autoload = $autoload;
    }

    public function supports(string $type): bool
    {
        $type = $this->removeWhitespaces($type);
        return \class_exists($type, $this->autoload) || \interface_exists($type, $this->autoload);
    }

    public function make(string $type): TypeInterface
    {
        $type = $this->removeWhitespaces($type);

        if (!$this->supports($type)) {
            throw new TypeNotSupportedException($type, InstanceOfType::class);
        }

        /** @phpstan-var class-string $type */
        return InstanceOfType::new($type);
    }
}
