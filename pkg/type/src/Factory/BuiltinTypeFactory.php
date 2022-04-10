<?php

declare(strict_types=1);

namespace Warp\Type\Factory;

use Warp\Type\BuiltinType;
use Warp\Type\Exception\TypeNotSupportedException;
use Warp\Type\TypeInterface;

final class BuiltinTypeFactory implements TypeFactoryInterface
{
    use TypeFactoryTrait;

    public function supports(string $type): bool
    {
        $type = $this->prepareType($type);

        return \in_array($type, BuiltinType::ALL, true);
    }

    public function make(string $type): TypeInterface
    {
        $type = $this->prepareType($type);

        if (!$this->supports($type)) {
            throw new TypeNotSupportedException($type, BuiltinType::class);
        }

        return BuiltinType::new($type);
    }

    private function prepareType(string $type): string
    {
        $type = \strtolower($this->removeWhitespaces($type));

        if (\str_starts_with($type, 'resource')) {
            $type = BuiltinType::RESOURCE;
        }

        $map = [
            'boolean' => BuiltinType::BOOL,
            'integer' => BuiltinType::INT,
            'double' => BuiltinType::FLOAT,
        ];

        return $map[$type] ?? $type;
    }
}
