<?php

declare(strict_types=1);

namespace Warp\Common\Field;

use PHPUnit\Framework\TestCase;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyAccess\PropertyPath;

class PropertyAccessFieldTest extends TestCase
{
    private function makePropertyAccessor(): PropertyAccessorInterface
    {
        return PropertyAccess::createPropertyAccessorBuilder()->getPropertyAccessor();
    }

    public function testDefault(): void
    {
        $fieldKey = new PropertyAccessField('[name]', $this->makePropertyAccessor());
        $fieldProperty = new PropertyAccessField(new PropertyPath('name'), $this->makePropertyAccessor());

        self::assertSame('[name]', (string)$fieldKey);
        self::assertSame(['name'], $fieldKey->getElements());
        self::assertSame('name', (string)$fieldProperty);
        self::assertSame(['name'], $fieldProperty->getElements());

        $array = [
            'name' => 'John Doe',
        ];

        $arrayAccess = new \ArrayObject($array, \ArrayObject::ARRAY_AS_PROPS);
        $object = (object)$array;

        self::assertSame('John Doe', $fieldKey->extract($array));
        self::assertSame('John Doe', $fieldKey->extract($arrayAccess));
        self::assertSame('John Doe', $fieldProperty->extract($object));
    }

    public function testNestedDot(): void
    {
        $field = new PropertyAccessField('user.name', $this->makePropertyAccessor());

        self::assertSame('user.name', (string)$field);
        self::assertSame(['user', 'name'], $field->getElements());

        $object = (object)[
            'user' => (object)[
                'name' => 'John Doe',
            ],
        ];

        self::assertSame('John Doe', $field->extract($object));
    }

    public function testNestedArrayKey(): void
    {
        $field = new PropertyAccessField('[user][name]', $this->makePropertyAccessor());

        self::assertSame('[user][name]', (string)$field);
        self::assertSame(['user', 'name'], $field->getElements());

        $array = [
            'user' => [
                'name' => 'John Doe',
            ],
        ];

        $arrayAccess = new \ArrayObject($array, \ArrayObject::ARRAY_AS_PROPS);

        self::assertSame('John Doe', $field->extract($array));
        self::assertSame('John Doe', $field->extract($arrayAccess));
    }

    public function testExtractNotReadable(): void
    {
        $field = new PropertyAccessField('property', $this->makePropertyAccessor());

        self::assertNull($field->extract((object)[]));
    }
}
