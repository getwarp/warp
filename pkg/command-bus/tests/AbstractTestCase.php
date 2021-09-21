<?php

declare(strict_types=1);

namespace spaceonfire\CommandBus;

use PHPUnit\Framework\TestCase;

if (trait_exists('\Prophecy\PhpUnit\ProphecyTrait')) {
    abstract class AbstractTestCase extends TestCase
    {
        use \Prophecy\PhpUnit\ProphecyTrait;
    }
} else {
    abstract class AbstractTestCase extends TestCase
    {
    }
}
