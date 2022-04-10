<?php

declare(strict_types=1);

namespace Warp\CommandBus\Middleware\Logger;

trait MayBeLoggedMessageTrait
{
    private ?string $beforeMessage = null;

    private ?string $afterMessage = null;

    private ?string $errorMessage = null;

    public function renderBeforeMessage(): ?string
    {
        return $this->renderMessage($this->beforeMessage);
    }

    public function renderAfterMessage(): ?string
    {
        return $this->renderMessage($this->afterMessage);
    }

    public function renderErrorMessage(): ?string
    {
        return $this->renderMessage($this->errorMessage);
    }

    public function setBeforeMessage(?string $beforeMessage): void
    {
        $this->beforeMessage = $beforeMessage;
    }

    public function setAfterMessage(?string $afterMessage): void
    {
        $this->afterMessage = $afterMessage;
    }

    public function setErrorMessage(?string $errorMessage): void
    {
        $this->errorMessage = $errorMessage;
    }

    protected function renderMessage(?string $message): ?string
    {
        if (null === $message) {
            return null;
        }

        return \str_replace('{command}', static::class, $message);
    }
}
