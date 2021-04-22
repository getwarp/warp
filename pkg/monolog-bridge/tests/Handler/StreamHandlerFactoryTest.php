<?php

declare(strict_types=1);

namespace spaceonfire\MonologBridge\Handler;

use Monolog\Logger;
use Monolog\Test\TestCase;
use spaceonfire\MonologBridge\Fixture\FixtureFormatter;

class StreamHandlerFactoryTest extends TestCase
{
    /**
     * @var string[]
     */
    private array $tempFiles = [];

    public function __destruct()
    {
        foreach ($this->tempFiles as $file) {
            \unlink($file);
        }
    }

    private function tempFile(): string
    {
        $file = \tempnam(\sys_get_temp_dir(), 'StreamHandlerFactoryTest');
        \assert(false !== $file);
        $this->tempFiles[] = $file;
        return $file;
    }

    public function testDefault(): void
    {
        $factory = new StreamHandlerFactory();

        self::assertContains('stream', $factory->supportedTypes());

        $file = $this->tempFile();

        $handler = $factory->make([
            'stream' => $file,
            'formatter' => FixtureFormatter::class,
        ]);

        $handler->handle($this->getRecord(Logger::WARNING, 'warning'));

        $log = \file_get_contents($file);

        self::assertSame('warning', $log);
    }
}
