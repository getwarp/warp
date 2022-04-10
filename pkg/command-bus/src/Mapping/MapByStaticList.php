<?php

declare(strict_types=1);

namespace Warp\CommandBus\Mapping;

use Warp\CommandBus\Exception\FailedToMapCommandException;

final class MapByStaticList implements CommandToHandlerMappingInterface
{
    /**
     * @var array<class-string,array{class-string,string}>
     */
    private array $mapping;

    /**
     * @param array<class-string,array{class-string,string}> $mapping
     * @example
     *     ```php
     *     new MapByStaticList([
     *         SomeCommand::class => [SomeHandler::class, 'handle'],
     *         OtherCommand::class => [WhateverHandler::class, 'handleOtherCommand'],
     *         ...
     *     ])
     *     ```
     */
    public function __construct(array $mapping)
    {
        $this->mapping = $mapping;
    }

    public function getClassName(string $commandClass): string
    {
        if (!\array_key_exists($commandClass, $this->mapping)) {
            throw FailedToMapCommandException::className($commandClass);
        }

        return $this->mapping[$commandClass][0];
    }

    public function getMethodName(string $commandClass): string
    {
        if (!\array_key_exists($commandClass, $this->mapping)) {
            throw FailedToMapCommandException::methodName($commandClass);
        }

        return $this->mapping[$commandClass][1];
    }
}
