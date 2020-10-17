<?php

declare(strict_types=1);

namespace spaceonfire\Type\Factory;

use spaceonfire\Type\Exception\TypeNotSupportedException;
use spaceonfire\Type\Type;

final class GroupTypeFactory implements TypeFactoryInterface
{
    use TypeFactoryTrait;

    /**
     * @inheritDoc
     */
    public function supports(string $type): bool
    {
        if ($this->parent === null) {
            return false;
        }

        $type = $this->removeWhitespaces($type);

        return
            strlen($type) > 2 &&
            $type[0] === '(' &&
            strrev($type)[0] === ')' &&
            $this->parent->supports(substr($type, 1, -1));
    }

    /**
     * @inheritDoc
     */
    public function make(string $type): Type
    {
        $type = $this->removeWhitespaces($type);

        if (!$this->supports($type)) {
            throw new TypeNotSupportedException($type);
        }

        return $this->parent->make(substr($type, 1, -1));
    }
}
