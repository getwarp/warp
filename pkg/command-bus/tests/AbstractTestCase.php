<?php

declare(strict_types=1);

namespace spaceonfire\CommandBus;

use PHPUnit\Framework\TestCase;

abstract class AbstractTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->markTestSkipped();
    }
}
