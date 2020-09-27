<?php

declare(strict_types=1);

namespace spaceonfire\CommandBus\Bridge\PsrLog;

interface MayBeLoggedMessage
{
    /**
     * Renders log message that be logged before message handling
     * @return string|null log message or null if need to skip logging
     */
    public function renderBeforeMessage(): ?string;

    /**
     * Renders log message that be logged after message handling
     * @return string|null log message or null if need to skip logging
     */
    public function renderAfterMessage(): ?string;

    /**
     * Render log message when error occurred while message handling
     * @return string|null log message (if null returned default template will be used)
     */
    public function renderErrorMessage(): ?string;
}
