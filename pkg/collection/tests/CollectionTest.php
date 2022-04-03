<?php /** @noinspection ReturnTypeCanBeDeclaredInspection */

declare(strict_types=1);

namespace Warp\Collection;

class CollectionTest extends AbstractCollectionTest
{
    protected function factory($items = []): CollectionInterface
    {
        return new Collection($items);
    }
}
