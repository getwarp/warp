<?php

declare(strict_types=1);

namespace spaceonfire\Collection;

use RuntimeException;
use stdClass;

class TypedCollection extends BaseCollection
{
    /**
     * @var string
     */
    protected $type;

    /**
     * TypedCollection constructor.
     * @param array $items
     * @param string $type Full qualified name of type
     */
    public function __construct($items = [], string $type = stdClass::class)
    {
        $this->type = $type;
        parent::__construct($items);
    }

    /** {@inheritDoc} */
    protected function getItems($items): array
    {
        $result = parent::getItems($items);
        foreach ($result as $item) {
            $type = gettype($item);

            if (($type === 'object') && !class_exists($this->type)) {
                throw new RuntimeException('Class ' . $this->type . ' does not exist');
            }

            if (($type === 'object' && !($item instanceof $this->type)) ||
                ($type !== 'object' && $type !== $this->type)) {
                throw new RuntimeException(static::class . ' accept only instances of ' . $this->type);
            }
        }
        return $result;
    }
}
