<?php

declare(strict_types=1);

namespace Warp\Container\Exception;

use InvalidArgumentException;
use Psr\Container\NotFoundExceptionInterface;

final class NotFoundException extends InvalidArgumentException implements NotFoundExceptionInterface
{
}
