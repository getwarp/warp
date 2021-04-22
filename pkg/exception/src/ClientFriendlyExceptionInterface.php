<?php

declare(strict_types=1);

namespace spaceonfire\Exception;

use Yiisoft\FriendlyException\FriendlyExceptionInterface;

/**
 * Marks exception that can be safely shown to user.
 */
interface ClientFriendlyExceptionInterface extends FriendlyExceptionInterface
{
}
