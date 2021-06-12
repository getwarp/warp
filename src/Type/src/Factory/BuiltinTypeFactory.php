<?php

declare(strict_types=1);

namespace spaceonfire\Type\Factory;

use spaceonfire\Type\BuiltinType;
use spaceonfire\Type\Exception\TypeNotSupportedException;
use spaceonfire\Type\TypeInterface;

final class BuiltinTypeFactory implements TypeFactoryInterface
{
    use TypeFactoryTrait;

    /**
     * @var bool
     */
    private $strictByDefault;

    /**
     * BuiltinTypeFactory constructor.
     * @param bool $strictByDefault
     */
    public function __construct(bool $strictByDefault = true)
    {
        $this->strictByDefault = $strictByDefault;
    }

    /**
     * @inheritDoc
     */
    public function supports(string $type): bool
    {
        $type = $this->prepareType($type);

        return in_array($type, BuiltinType::ALL, true);
    }

    /**
     * @inheritDoc
     */
    public function make(string $type): TypeInterface
    {
        $type = $this->prepareType($type);

        if (!$this->supports($type)) {
            throw new TypeNotSupportedException($type, BuiltinType::class);
        }

        return new BuiltinType($type, $this->prepareStrictArgument($type));
    }

    private function prepareType(string $type): string
    {
        $type = strtolower($this->removeWhitespaces($type));

        if (0 === strpos($type, 'resource')) {
            $type = BuiltinType::RESOURCE;
        }

        $map = [
            'boolean' => BuiltinType::BOOL,
            'integer' => BuiltinType::INT,
            'double' => BuiltinType::FLOAT,
        ];

        return $map[$type] ?? $type;
    }

    private function prepareStrictArgument(string $type): bool
    {
        return false === $this->strictByDefault && !isset(BuiltinType::SCALAR_TYPES[$type])
            ? true
            : $this->strictByDefault;
    }
}
