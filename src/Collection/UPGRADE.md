# Upgrading Instruction

## Upgrade from 1.x to 2.0

-   `BaseCollection` has been removed. Extend `AbstractCollectionDecorator` if you need to add new functionality to
    collections.
-   `TypedCollection` now final. Use `TypedCollection` inside your `AbstractCollectionDecorator` if you need to add new
    functionality to collections. For example:

```php
use spaceonfire\Collection\AbstractCollectionDecorator;
use spaceonfire\Collection\TypedCollection;

final class MyIntegerCollection extends AbstractCollectionDecorator
{
    public function __construct($items) {
        parent::__construct(new TypedCollection($items, 'integer'));
    }

    // New functionality
    public function multiplyByThree(): self
    {
        return $this->map(static function ($i) {
            return $i * 3;
        });
    }
}
```
