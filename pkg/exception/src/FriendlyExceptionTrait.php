<?php

declare(strict_types=1);

namespace spaceonfire\Exception;

use function Symfony\Component\String\s;

trait FriendlyExceptionTrait
{
    /**
     * @var string|\Stringable|null
     */
    protected $name = null;

    /**
     * @var string|\Stringable|null
     */
    protected $solution = null;

    public function getName(): string
    {
        return (string)($this->name ?? static::getDefaultName());
    }

    public function getSolution(): ?string
    {
        return null === $this->solution ? null : (string)$this->solution;
    }

    protected static function getDefaultName(): string
    {
        $classname = '\\' . static::class;
        $rightSlash = \strrpos($classname, '\\') ?: 0;
        $shortName = \substr($classname, $rightSlash);

        return s($shortName)->snake()->replace('_', ' ')->title()->toString();
    }
}
