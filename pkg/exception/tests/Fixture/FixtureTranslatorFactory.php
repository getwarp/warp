<?php

declare(strict_types=1);

namespace Warp\Exception\Fixture;

use Symfony\Contracts\Translation\TranslatorInterface;

final class FixtureTranslatorFactory
{
    private function __construct()
    {
    }

    public static function new(): self
    {
        return new self();
    }

    public function make(array $translations, string $locale = 'en', ?string $domain = null): TranslatorInterface
    {
        return new class($translations, $locale, $domain) implements TranslatorInterface {
            private array $translations;
            private string $locale;
            private ?string $domain;

            public function __construct(array $translations, string $locale = 'en', ?string $domain = null)
            {
                $this->translations = $translations;
                $this->locale = $locale;
                $this->domain = $domain;
            }

            public function getLocale(): string
            {
                return $this->locale;
            }

            public function trans(?string $id, array $parameters = [], ?string $domain = null, ?string $locale = null): string
            {
                if ($this->domain !== $domain) {
                    throw new \RuntimeException('Wrong domain.');
                }

                return \strtr($this->translations[$locale ?? $this->locale][$id] ?? $id ?? '', $parameters);
            }
        };
    }
}
