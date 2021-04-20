<?php

declare(strict_types=1);

namespace spaceonfire\CommandBus\Bridge\PsrLog;

trait MayBeLoggedMessageTrait
{
    /**
     * @var string|null
     */
    private $beforeMessage;

    /**
     * @var string|null
     */
    private $afterMessage;

    /**
     * @var string|null
     */
    private $errorMessage;

    /**
     * Renders log message that be logged before message handling
     * @return string|null log message or null if need to skip logging
     */
    public function renderBeforeMessage(): ?string
    {
        if ($this->beforeMessage) {
            return str_replace('{command}', static::class, $this->beforeMessage);
        }

        return null;
    }

    /**
     * Renders log message that be logged after message handling
     * @return string|null log message or null if need to skip logging
     */
    public function renderAfterMessage(): ?string
    {
        if ($this->afterMessage) {
            return str_replace('{command}', static::class, $this->afterMessage);
        }

        return null;
    }

    /**
     * Render log message when error occurred while message handling
     * @return string|null log message (if null returned default template will be used)
     */
    public function renderErrorMessage(): ?string
    {
        if ($this->errorMessage) {
            return str_replace('{command}', static::class, $this->errorMessage);
        }

        return null;
    }

    /**
     * Setter for `beforeMessage` property
     * @param string|null $beforeMessage
     * @return static
     */
    public function setBeforeMessage(?string $beforeMessage)
    {
        $this->beforeMessage = $beforeMessage;
        return $this;
    }

    /**
     * Setter for `afterMessage` property
     * @param string|null $afterMessage
     * @return static
     */
    public function setAfterMessage(?string $afterMessage)
    {
        $this->afterMessage = $afterMessage;
        return $this;
    }

    /**
     * Setter for `errorMessage` property
     * @param string|null $errorMessage
     * @return static
     */
    public function setErrorMessage(?string $errorMessage)
    {
        $this->errorMessage = $errorMessage;
        return $this;
    }
}
