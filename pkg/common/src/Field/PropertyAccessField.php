<?php

declare(strict_types=1);

namespace spaceonfire\Common\Field;

use spaceonfire\Exception\PackageMissingException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyAccess\PropertyPath;

final class PropertyAccessField implements FieldInterface
{
    /**
     * @var PropertyPath<array-key>
     */
    private PropertyPath $propertyPath;

    private PropertyAccessorInterface $propertyAccessor;

    /**
     * @param string|PropertyPath<array-key> $propertyPath
     * @param PropertyAccessorInterface|null $propertyAccessor
     */
    public function __construct($propertyPath, ?PropertyAccessorInterface $propertyAccessor = null)
    {
        if (!\class_exists(PropertyAccess::class)) {
            throw PackageMissingException::new('symfony/property-access', null, self::class);
        }

        if (!$propertyPath instanceof PropertyPath) {
            $propertyPath = new PropertyPath($propertyPath);
        }

        $this->propertyPath = $propertyPath;
        $this->propertyAccessor = $propertyAccessor ?? PropertyAccessFieldFactory::getDefaultPropertyAccessor();
    }

    public function __toString(): string
    {
        return (string)$this->propertyPath;
    }

    public function getElements(): array
    {
        return $this->propertyPath->getElements();
    }

    public function extract($element)
    {
        if (!$this->propertyAccessor->isReadable($element, $this->propertyPath)) {
            return null;
        }

        return $this->propertyAccessor->getValue($element, $this->propertyPath);
    }
}
