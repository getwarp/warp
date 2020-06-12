<?php

declare(strict_types=1);

namespace spaceonfire\DataSource;

use spaceonfire\DataSource\Adapters\NetteUtils\SmartArrayAccessObject;

abstract class AbstractEntity implements EntityInterface
{
    use SmartArrayAccessObject, JsonSerializableObjectTrait;
}
