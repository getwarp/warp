<?php

declare(strict_types=1);

namespace spaceonfire\Common\Field;

use spaceonfire\Exception\PackageMissingException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyAccess\PropertyPath;

final class PropertyAccessFieldFactory implements FieldFactoryInterface
{
    private static ?PropertyAccessorInterface $defaultPropertyAccessor = null;

    private PropertyAccessorInterface $propertyAccessor;

    public function __construct(?PropertyAccessorInterface $propertyAccessor = null)
    {
        $this->propertyAccessor = $propertyAccessor ?? self::getDefaultPropertyAccessor();
    }

    public function setPropertyAccessor(PropertyAccessorInterface $propertyAccessor): void
    {
        $this->propertyAccessor = $propertyAccessor;
    }

    public function enabled(): bool
    {
        return \class_exists(PropertyAccess::class);
    }

    public function make(string $field): PropertyAccessField
    {
        if (!$this->enabled()) {
            throw PackageMissingException::new('symfony/property-access', null, self::class);
        }

        return new PropertyAccessField(new PropertyPath($field), $this->propertyAccessor);
    }

    public static function getDefaultPropertyAccessor(): PropertyAccessorInterface
    {
        return self::$defaultPropertyAccessor ??= PropertyAccess::createPropertyAccessor();
    }
}
