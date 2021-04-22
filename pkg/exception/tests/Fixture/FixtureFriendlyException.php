<?php

declare(strict_types=1);

namespace spaceonfire\Exception\Fixture;

use spaceonfire\Exception\FriendlyExceptionTrait;
use Throwable;
use Yiisoft\FriendlyException\FriendlyExceptionInterface;

final class FixtureFriendlyException extends \Exception implements FriendlyExceptionInterface
{
    use FriendlyExceptionTrait;

    private function __construct($message = '', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function new($message = '', $name = null, $solution = null): self
    {
        $exception = new self($message);
        $exception->name = $name;
        $exception->solution = $solution;
        return $exception;
    }
}
