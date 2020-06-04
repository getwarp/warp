<?php /** @noinspection ReturnTypeCanBeDeclaredInspection */

declare(strict_types=1);

namespace spaceonfire\Collection;

class AbstractCollectionDecoratorTest extends AbstractCollectionTest
{
    /**
     * @param array $items
     * @return AbstractCollectionDecorator
     */
    protected function factory($items = []): CollectionInterface
    {
        return new class($items) extends AbstractCollectionDecorator {
        };
    }

    public function testDowngrade(): void
    {
        $outerCollection = $this->factory($innerCollection = $this->factory());
        self::assertEquals($innerCollection, $outerCollection->downgrade());
        self::assertEquals($innerCollection->downgrade(), $outerCollection->downgrade(true));
    }
}
