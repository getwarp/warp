<?php

declare(strict_types=1);

namespace spaceonfire\Exception;

use Symfony\Contracts\Translation\TranslatorInterface;

trait TranslatableExceptionTrait
{
    protected MessageTemplate $template;

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return $this->template->trans($translator, $locale);
    }

    /**
     * @param MessageTemplate|scalar|\Stringable $message
     */
    protected function construct($message = '', int $code = 0, ?\Throwable $previous = null): void
    {
        $this->template = MessageTemplate::wrap($message);

        parent::__construct((string)$this->template, $code, $previous);
    }
}
