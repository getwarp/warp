<?php

declare(strict_types=1);

namespace Warp\DataSource;

use Warp\DataSource\Bridge\NetteUtils\SmartArrayAccessObject;

abstract class AbstractEntity implements EntityInterface, \ArrayAccess, \JsonSerializable
{
    use SmartArrayAccessObject;

    use JsonSerializableObjectTrait;
}
