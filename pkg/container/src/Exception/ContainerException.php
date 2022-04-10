<?php

declare(strict_types=1);

namespace Warp\Container\Exception;

use Psr\Container\ContainerExceptionInterface;
use Warp\Exception\FriendlyExceptionTrait;
use Yiisoft\FriendlyException\FriendlyExceptionInterface;

class ContainerException extends \RuntimeException implements ContainerExceptionInterface, FriendlyExceptionInterface
{
    use FriendlyExceptionTrait;

    public static function wrap(\Throwable $e, ?string $message = null): self
    {
        return new self($message ?? $e->getMessage(), $e->getCode(), $e);
    }
}
