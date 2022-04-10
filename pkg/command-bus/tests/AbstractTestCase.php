<?php

declare(strict_types=1);

namespace Warp\CommandBus;

use PHPUnit\Framework\TestCase;

abstract class AbstractTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->markTestSkipped();
    }
}
