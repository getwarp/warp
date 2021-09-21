<?php

declare(strict_types=1);

namespace spaceonfire\CommandBus\Mapping;

use function array_key_exists;

/**
 * Stupid simple command-to-handler mapper.
 */
final class MapByStaticList implements CommandToHandlerMappingInterface
{
    /**
     * @var array<string, array<string>>
     */
    private $mapping;

    /**
     * MapByStaticList constructor.
     * @param array<string, array<string>> $mapping
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

    /**
     * @inheritDoc
     */
    public function getClassName(string $commandClassName): string
    {
        if (!array_key_exists($commandClassName, $this->mapping)) {
            throw FailedToMapCommand::className($commandClassName);
        }

        return $this->mapping[$commandClassName][0];
    }

    /**
     * @inheritDoc
     */
    public function getMethodName(string $commandClassName): string
    {
        if (!array_key_exists($commandClassName, $this->mapping)) {
            throw FailedToMapCommand::methodName($commandClassName);
        }

        return $this->mapping[$commandClassName][1];
    }
}
