<?php /** @noinspection ReturnTypeCanBeDeclaredInspection */

declare(strict_types=1);

namespace spaceonfire\Collection;

class CollectionTest extends AbstractCollectionTest
{
    protected function factory($items = []): CollectionInterface
    {
        return new Collection($items);
    }
}
