<?php

declare(strict_types=1);

namespace spaceonfire\DataSource;

use ArrayAccess;
use JsonSerializable;

interface EntityInterface extends ArrayAccess, JsonSerializable
{
}
