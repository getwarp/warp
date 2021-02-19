<?php

declare(strict_types=1);

namespace spaceonfire\Container;

use PHPUnit\Framework\TestCase;

if (trait_exists('\Prophecy\PhpUnit\ProphecyTrait')) {
    abstract class AbstractTestCase extends TestCase
    {
        use \Prophecy\PhpUnit\ProphecyTrait;
        use WithContainerMockTrait;
    }
} else {
    abstract class AbstractTestCase extends TestCase
    {
        use WithContainerMockTrait;
    }
}
