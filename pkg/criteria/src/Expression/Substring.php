<?php

declare(strict_types=1);

namespace Warp\Criteria\Expression;

use Webmozart\Expression\Constraint\Contains;
use Webmozart\Expression\Constraint\EndsWith;
use Webmozart\Expression\Constraint\StartsWith;
use Webmozart\Expression\Expression;
use Webmozart\Expression\Logic\Literal;
use Webmozart\Expression\Util\StringUtil;

/**
 * @phpstan-type SubstringMode=self::CONTAINS|self::STARTS_WITH|self::ENDS_WITH
 */
final class Substring extends Literal
{
    public const CONTAINS = 0;
    public const STARTS_WITH = 1;
    public const ENDS_WITH = 2;

    private string $substring;
    private int $substringLength;
    private bool $caseSensitive;

    /**
     * @var SubstringMode
     */
    private int $mode;

    /**
     * @param SubstringMode $mode
     */
    private function __construct(string $substring, bool $caseSensitive, int $mode)
    {
        $this->substring = $substring;
        $this->substringLength = \strlen($this->substring);
        $this->caseSensitive = $caseSensitive;
        $this->mode = $mode;
    }

    public static function startsWith(string $substring, bool $caseSensitive = true): self
    {
        return new self($substring, $caseSensitive, self::STARTS_WITH);
    }

    public static function endsWith(string $substring, bool $caseSensitive = true): self
    {
        return new self($substring, $caseSensitive, self::ENDS_WITH);
    }

    public static function contains(string $substring, bool $caseSensitive = true): self
    {
        return new self($substring, $caseSensitive, self::CONTAINS);
    }

    public function evaluate($value): bool
    {
        if ('' === $this->substring || $value === $this->substring) {
            return true;
        }

        if (self::STARTS_WITH === $this->mode) {
            return 0 === \substr_compare($value, $this->substring, 0, $this->substringLength, !$this->caseSensitive);
        }

        if (self::ENDS_WITH === $this->mode) {
            return 0 === \substr_compare(
                $value,
                $this->substring,
                -$this->substringLength,
                $this->substringLength,
                !$this->caseSensitive,
            );
        }

        if ($this->caseSensitive) {
            return false !== \strpos($value, $this->substring);
        }

        return false !== \stripos($value, $this->substring);
    }

    public function equivalentTo(Expression $other): bool
    {
        if (!$this->caseSensitive) {
            return $other instanceof $this
                && !$other->caseSensitive
                && $other->substringLength === $this->substringLength
                && $other->mode === $this->mode
                && 0 === \substr_compare($other->substring, $this->substring, 0, $this->substringLength, true);
        }

        if ($other instanceof $this) {
            return $other->caseSensitive && $other->mode === $this->mode && $other->substring === $this->substring;
        }

        if ($other instanceof Contains) {
            return self::CONTAINS === $this->mode && $other->getComparedValue() === $this->substring;
        }

        if ($other instanceof StartsWith) {
            return self::STARTS_WITH === $this->mode && $other->getAcceptedPrefix() === $this->substring;
        }

        if ($other instanceof EndsWith) {
            return self::ENDS_WITH === $this->mode && $other->getAcceptedSuffix() === $this->substring;
        }

        return false;
    }

    public function toString(): string
    {
        $arg = StringUtil::formatValue($this->substring);

        if (self::STARTS_WITH === $this->mode) {
            $func = 'startsWith';
        } elseif (self::ENDS_WITH === $this->mode) {
            $func = 'endsWith';
        } else {
            $func = 'contains';
        }

        if (!$this->caseSensitive) {
            $func .= 'Insensitive';
        }

        return \sprintf('%s(%s)', $func, $arg);
    }

    public function getSubstring(): string
    {
        return $this->substring;
    }

    public function isCaseSensitive(): bool
    {
        return $this->caseSensitive;
    }

    /**
     * @return SubstringMode
     */
    public function getMode(): int
    {
        return $this->mode;
    }
}
