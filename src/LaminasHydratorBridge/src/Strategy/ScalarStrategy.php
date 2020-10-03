<?php

declare(strict_types=1);

namespace spaceonfire\LaminasHydratorBridge\Strategy;

use InvalidArgumentException;
use Laminas\Hydrator\Strategy\StrategyInterface;
use spaceonfire\Type\BuiltinType;

final class ScalarStrategy implements StrategyInterface
{
    /**
     * @var BuiltinType
     */
    private $hydrateType;
    /**
     * @var BuiltinType
     */
    private $extractType;

    /**
     * ScalarStrategy constructor.
     * @param string $hydrateType
     * @param string|null $extractType
     */
    public function __construct(string $hydrateType, ?string $extractType = null)
    {
        if (!isset(BuiltinType::SCALAR_TYPES[$hydrateType])) {
            throw new InvalidArgumentException(sprintf(
                'Argument $hydrateType expected to be one of: %s. Got: "%s"',
                implode(', ', array_keys(BuiltinType::SCALAR_TYPES)),
                $hydrateType
            ));
        }

        if ($extractType !== null && !isset(BuiltinType::SCALAR_TYPES[$extractType])) {
            throw new InvalidArgumentException(sprintf(
                'Argument $extractType expected to be null or one of: %s. Got: "%s"',
                implode(', ', array_keys(BuiltinType::SCALAR_TYPES)),
                $hydrateType
            ));
        }

        $this->hydrateType = BuiltinType::create($hydrateType, false);
        $this->extractType = $extractType === null
            ? $this->hydrateType
            : BuiltinType::create($extractType, false);
    }

    /**
     * @inheritDoc
     */
    public function extract($value, ?object $object = null)
    {
        if (!$this->extractType->check($value)) {
            throw new InvalidArgumentException(sprintf(
                'Unable to extract. Expected a %s. Got: %s.',
                $this->extractType,
                is_object($value) ? get_class($value) : gettype($value)
            ));
        }

        return $this->extractType->cast($value);
    }

    /**
     * @inheritDoc
     */
    public function hydrate($value, ?array $data)
    {
        if (!$this->hydrateType->check($value)) {
            throw new InvalidArgumentException(sprintf(
                'Unable to extract. Expected a %s. Got: %s.',
                $this->hydrateType,
                is_object($value) ? get_class($value) : gettype($value)
            ));
        }

        return $this->hydrateType->cast($value);
    }
}
