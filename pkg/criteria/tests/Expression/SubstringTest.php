<?php

declare(strict_types=1);

namespace Warp\Criteria\Expression;

use PHPUnit\Framework\TestCase;
use Webmozart\Expression\Constraint\Contains;
use Webmozart\Expression\Constraint\EndsWith;
use Webmozart\Expression\Constraint\StartsWith;
use Webmozart\Expression\Logic\AlwaysTrue;

final class SubstringTest extends TestCase
{
    public function testContains(): void
    {
        $sensitive = Substring::contains('foo');

        self::assertTrue($sensitive->evaluate('foo'));
        self::assertTrue($sensitive->evaluate('foobar'));
        self::assertTrue($sensitive->evaluate('barfoo'));
        self::assertTrue($sensitive->evaluate('barfoobaz'));
        self::assertFalse($sensitive->evaluate('FOO'));
        self::assertFalse($sensitive->evaluate('FOOBAR'));
        self::assertFalse($sensitive->evaluate('BARFOO'));
        self::assertFalse($sensitive->evaluate('BARFOOBAZ'));
        self::assertFalse($sensitive->evaluate('bar'));

        $insensitive = Substring::contains('foo', false);

        self::assertTrue($insensitive->evaluate('foo'));
        self::assertTrue($insensitive->evaluate('foobar'));
        self::assertTrue($insensitive->evaluate('barfoo'));
        self::assertTrue($insensitive->evaluate('barfoobaz'));
        self::assertTrue($insensitive->evaluate('FOO'));
        self::assertTrue($insensitive->evaluate('FOOBAR'));
        self::assertTrue($insensitive->evaluate('BARFOO'));
        self::assertTrue($insensitive->evaluate('BARFOOBAZ'));
        self::assertFalse($insensitive->evaluate('bar'));

        $long = Substring::contains('foobar');
        self::assertFalse($long->evaluate('foo'));

        $longInsensitive = Substring::contains('foobar', false);
        self::assertFalse($longInsensitive->evaluate('foo'));
        self::assertFalse($longInsensitive->evaluate('FOO'));

        self::assertSame('contains("foo")', $sensitive->toString());
        self::assertSame('containsInsensitive("foo")', $insensitive->toString());

        self::assertSame('foo', $sensitive->getSubstring());
        self::assertSame(Substring::CONTAINS, $sensitive->getMode());
        self::assertTrue($sensitive->isCaseSensitive());
        self::assertFalse($insensitive->isCaseSensitive());
    }

    public function testStartsWith(): void
    {
        $sensitive = Substring::startsWith('foo');

        self::assertTrue($sensitive->evaluate('foo'));
        self::assertTrue($sensitive->evaluate('foobar'));
        self::assertFalse($sensitive->evaluate('barfoo'));
        self::assertFalse($sensitive->evaluate('barfoobaz'));
        self::assertFalse($sensitive->evaluate('FOO'));
        self::assertFalse($sensitive->evaluate('FOOBAR'));
        self::assertFalse($sensitive->evaluate('BARFOO'));
        self::assertFalse($sensitive->evaluate('BARFOOBAZ'));
        self::assertFalse($sensitive->evaluate('bar'));

        $insensitive = Substring::startsWith('foo', false);

        self::assertTrue($insensitive->evaluate('foo'));
        self::assertTrue($insensitive->evaluate('foobar'));
        self::assertFalse($insensitive->evaluate('barfoo'));
        self::assertFalse($insensitive->evaluate('barfoobaz'));
        self::assertTrue($insensitive->evaluate('FOO'));
        self::assertTrue($insensitive->evaluate('FOOBAR'));
        self::assertFalse($insensitive->evaluate('BARFOO'));
        self::assertFalse($insensitive->evaluate('BARFOOBAZ'));
        self::assertFalse($insensitive->evaluate('bar'));

        $long = Substring::startsWith('foobar');
        self::assertFalse($long->evaluate('foo'));

        $longInsensitive = Substring::startsWith('foobar', false);
        self::assertFalse($longInsensitive->evaluate('foo'));
        self::assertFalse($longInsensitive->evaluate('FOO'));

        self::assertSame('startsWith("foo")', $sensitive->toString());
        self::assertSame('startsWithInsensitive("foo")', $insensitive->toString());

        self::assertSame('foo', $sensitive->getSubstring());
        self::assertSame(Substring::STARTS_WITH, $sensitive->getMode());
        self::assertTrue($sensitive->isCaseSensitive());
        self::assertFalse($insensitive->isCaseSensitive());
    }

    public function testEndsWith(): void
    {
        $sensitive = Substring::endsWith('foo');

        self::assertTrue($sensitive->evaluate('foo'));
        self::assertFalse($sensitive->evaluate('foobar'));
        self::assertTrue($sensitive->evaluate('barfoo'));
        self::assertFalse($sensitive->evaluate('barfoobaz'));
        self::assertFalse($sensitive->evaluate('FOO'));
        self::assertFalse($sensitive->evaluate('FOOBAR'));
        self::assertFalse($sensitive->evaluate('BARFOO'));
        self::assertFalse($sensitive->evaluate('BARFOOBAZ'));
        self::assertFalse($sensitive->evaluate('bar'));

        $insensitive = Substring::endsWith('foo', false);

        self::assertTrue($insensitive->evaluate('foo'));
        self::assertFalse($insensitive->evaluate('foobar'));
        self::assertTrue($insensitive->evaluate('barfoo'));
        self::assertFalse($insensitive->evaluate('barfoobaz'));
        self::assertTrue($insensitive->evaluate('FOO'));
        self::assertFalse($insensitive->evaluate('FOOBAR'));
        self::assertTrue($insensitive->evaluate('BARFOO'));
        self::assertFalse($insensitive->evaluate('BARFOOBAZ'));
        self::assertFalse($insensitive->evaluate('bar'));

        $long = Substring::endsWith('foobar');
        self::assertFalse($long->evaluate('bar'));

        $longInsensitive = Substring::endsWith('foobar', false);
        self::assertFalse($longInsensitive->evaluate('bar'));
        self::assertFalse($longInsensitive->evaluate('BAR'));

        self::assertSame('endsWith("foo")', $sensitive->toString());
        self::assertSame('endsWithInsensitive("foo")', $insensitive->toString());

        self::assertSame('foo', $sensitive->getSubstring());
        self::assertSame(Substring::ENDS_WITH, $sensitive->getMode());
        self::assertTrue($sensitive->isCaseSensitive());
        self::assertFalse($insensitive->isCaseSensitive());
    }

    public function testEquivalentTo(): void
    {
        self::assertTrue(Substring::contains('foo')->equivalentTo(Substring::contains('foo')));
        self::assertTrue(Substring::startsWith('foo')->equivalentTo(Substring::startsWith('foo')));
        self::assertTrue(Substring::endsWith('foo')->equivalentTo(Substring::endsWith('foo')));
        self::assertTrue(Substring::contains('foo', false)->equivalentTo(Substring::contains('FoO', false)));

        self::assertFalse(Substring::contains('foo')->equivalentTo(Substring::contains('bar')));
        self::assertFalse(Substring::contains('foo')->equivalentTo(Substring::startsWith('foo')));
        self::assertFalse(Substring::contains('foo')->equivalentTo(Substring::contains('foo', false)));
        self::assertFalse(Substring::contains('foo', false)->equivalentTo(Substring::contains('fo', false)));
        self::assertFalse(Substring::contains('foo', false)->equivalentTo(Substring::startsWith('FoO', false)));
        self::assertFalse(Substring::contains('foo')->equivalentTo(new AlwaysTrue()));

        self::assertTrue(Substring::contains('foo')->equivalentTo(new Contains('foo')));
        self::assertTrue(Substring::startsWith('foo')->equivalentTo(new StartsWith('foo')));
        self::assertTrue(Substring::endsWith('foo')->equivalentTo(new EndsWith('foo')));
    }
}
