<?php

declare(strict_types=1);

namespace spaceonfire\DataSource;

use spaceonfire\DataSource\Bridge\NetteUtils\SmartArrayAccessObject;

abstract class AbstractEntity implements EntityInterface
{
    use SmartArrayAccessObject, JsonSerializableObjectTrait;
}
