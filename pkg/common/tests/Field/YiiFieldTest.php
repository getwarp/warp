<?php

declare(strict_types=1);

namespace spaceonfire\Common\Field;

use PHPUnit\Framework\TestCase;

class YiiFieldTest extends TestCase
{
    public function testDefault(): void
    {
        $field = new YiiField('name');

        self::assertSame('name', (string)$field);
        self::assertSame(['name'], $field->getElements());

        $array = [
            'name' => 'John Doe',
        ];

        $arrayAccess = new \ArrayObject($array, \ArrayObject::ARRAY_AS_PROPS);
        $object = (object)$array;

        self::assertSame('John Doe', $field->extract($array));
        self::assertSame('John Doe', $field->extract($arrayAccess));
        self::assertSame('John Doe', $field->extract($object));
    }

    public function testNestedDot(): void
    {
        $field = new YiiField('user.name');

        self::assertSame('user.name', (string)$field);
        self::assertSame(['user', 'name'], $field->getElements());

        $array = [
            'user' => [
                'name' => 'John Doe',
            ],
        ];

        $arrayAccess = new \ArrayObject($array, \ArrayObject::ARRAY_AS_PROPS);
        $object = (object)$array;
        $object->user = (object)$object->user;

        self::assertSame('John Doe', $field->extract($array));
        self::assertSame('John Doe', $field->extract($arrayAccess));
        self::assertSame('John Doe', $field->extract($object));
    }

    public function testNestedArrayKey(): void
    {
        $field = new YiiField('[user][name]');

        self::assertSame('[user][name]', (string)$field);
        self::assertSame(['user', 'name'], $field->getElements());

        $array = [
            'user' => [
                'name' => 'John Doe',
            ],
        ];

        $arrayAccess = new \ArrayObject($array, \ArrayObject::ARRAY_AS_PROPS);
        $object = (object)$array;
        $object->user = (object)$object->user;

        self::assertSame('John Doe', $field->extract($array));
        self::assertSame('John Doe', $field->extract($arrayAccess));
        self::assertSame('John Doe', $field->extract($object));
    }

    public function testCustomExtractKey(): void
    {
        $field = new YiiField('[user][name]', static fn () => 'Jane Doe');

        self::assertSame('[user][name]', (string)$field);
        self::assertSame(['user', 'name'], $field->getElements());

        $array = [
            'user' => [
                'name' => 'John Doe',
            ],
        ];

        self::assertSame('Jane Doe', $field->extract($array));
    }

    public function testEmptyStringNotAllowed(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new YiiField('');
    }

    /**
     * @dataProvider \spaceonfire\Common\Field\DefaultFieldTest::invalidFieldsProvider()
     * @param string $field
     */
    public function testInvalidField(string $field): void
    {
        $this->expectException(\LogicException::class);
        new DefaultField($field);
    }
}
