<?php

declare(strict_types=1);

namespace spaceonfire\DevTool\Monorepo\Composer\Synchronizer;

final class VersionConflict
{
    private string $message;

    /**
     * @var array<string,string>
     */
    private array $options;

    /**
     * @var callable
     */
    private $resolver;

    /**
     * VersionConflict constructor.
     * @param string $message
     * @param array<string,string> $options
     * @param callable $resolver
     */
    public function __construct(string $message, array $options, callable $resolver)
    {
        $this->message = $message;
        $this->options = $options;
        $this->resolver = $resolver;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return array<string,string>
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    public function resolve(string $value): void
    {
        ($this->resolver)($value);
    }
}
