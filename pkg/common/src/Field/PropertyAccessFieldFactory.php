<?php

declare(strict_types=1);

namespace Warp\Common\Field;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyAccess\PropertyPath;
use Warp\Exception\PackageMissingException;

final class PropertyAccessFieldFactory implements FieldFactoryInterface
{
    private static ?bool $enabled = null;

    private static ?PropertyAccessorInterface $defaultPropertyAccessor = null;

    private ?PropertyAccessorInterface $propertyAccessor;

    public function __construct(?PropertyAccessorInterface $propertyAccessor = null)
    {
        self::init();

        $this->propertyAccessor = $propertyAccessor ?? self::$defaultPropertyAccessor;
    }

    public function setPropertyAccessor(PropertyAccessorInterface $propertyAccessor): void
    {
        $this->propertyAccessor = $propertyAccessor;
    }

    public function enabled(): bool
    {
        return self::$enabled ?? false;
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
        self::init();

        if (null === self::$defaultPropertyAccessor) {
            throw PackageMissingException::new('symfony/property-access', null, self::class);
        }

        return self::$defaultPropertyAccessor;
    }

    private static function init(): void
    {
        self::$enabled ??= \class_exists(PropertyAccess::class);

        if (self::$enabled) {
            self::$defaultPropertyAccessor ??= PropertyAccess::createPropertyAccessor();
        }
    }
}
