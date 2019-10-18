<?php

declare(strict_types=1);

namespace spaceonfire\Collection;

use PHPUnit\Framework\TestCase;

class TypedCollectionTest extends TestCase
{
    public function testScalar()
    {
        new TypedCollection([0, 1, 2], 'integer');
    }

    public function testScalarException()
    {
        $this->expectException(\RuntimeException::class);
        new TypedCollection([0, 1, 2], 'string');
    }

    public function testObject()
    {
        new TypedCollection($this->getObjectsArray(), \stdClass::class);
    }

    public function testObjectException()
    {
        $this->expectException(\RuntimeException::class);
        new TypedCollection($this->getObjectsArray(), CollectionInterface::class);
    }

    public function testObjectClassExistException()
    {
        $this->expectException(\RuntimeException::class);
        new TypedCollection($this->getObjectsArray(), 'double');
    }

    protected function getObjectsArray(): array
    {
        $prototype = new class extends \stdClass
        {
        };

        $items = [];
        $items[] = clone $prototype;
        $items[] = clone $prototype;
        $items[] = clone $prototype;

        return $items;
    }
}
