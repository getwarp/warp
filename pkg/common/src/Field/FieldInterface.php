<?php

declare(strict_types=1);

namespace spaceonfire\Common\Field;

interface FieldInterface extends \Stringable
{
    /**
     * @return string[]
     */
    public function getElements(): array;

    /**
     * @param mixed $element
     * @return mixed|null
     */
    public function extract($element);
}
