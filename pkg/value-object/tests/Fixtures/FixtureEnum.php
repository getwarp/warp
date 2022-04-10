<?php

declare(strict_types=1);

namespace Warp\ValueObject\Fixtures;

use Warp\ValueObject\AbstractEnumValue;

/**
 * @method static self one()
 * @method static self twoWords()
 * @method static self manyManyWords()
 * @method static self oneString()
 */
final class FixtureEnum extends AbstractEnumValue
{
    public const ONE = 1;
    public const TWO_WORDS = 2;
    public const MANY_MANY_WORDS = 3;
    public const ONE_STRING = '1';
    private const PRIVATE = 'private';
}
