<?php

declare(strict_types=1);

namespace spaceonfire\DataSource\Exceptions;

use Throwable;
use Webmozart\Assert\Assert;

class DomainException extends \DomainException
{
    /**
     * @var array<string,string|mixed>
     */
    private $parameters;

    /**
     * DomainException constructor.
     * @param string|null $message
     * @param array $parameters
     * @param int $code
     * @param Throwable|null $previous
     */
    final public function __construct(
        ?string $message = null,
        array $parameters = [],
        int $code = 0,
        ?Throwable $previous = null
    ) {
        $this->parameters = $this->prepareParameters($parameters);
        $message = $message ?? $this->getDefaultMessage($this->parameters);
        parent::__construct($message, $code, $previous);
    }

    private function prepareParameters(array $parameters): array
    {
        Assert::allRegex(array_keys($parameters), '/[A-Za-z0-9_\-]+/i');

        $keys = array_map(static function ($key) {
            return '{' . $key . '}';
        }, array_keys($parameters));

        return array_combine($keys, $parameters) ?: [];
    }

    /**
     * Returns default exception message
     * @param array $parameters
     * @return string
     * @noinspection PhpUnusedParameterInspection
     */
    protected function getDefaultMessage(array $parameters = []): string
    {
        return 'Domain Exception';
    }

    /**
     * Getter for `parameters` property
     * @return array<string,string|mixed>
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * Renders throwable message for public
     * @param callable|null $prepareMessage
     * @return string
     */
    public function render(?callable $prepareMessage = null): string
    {
        $message = $prepareMessage ? $prepareMessage($this) : $this->getMessage();

        $parameters = array_map(static function ($value) {
            return (string)$value;
        }, $this->parameters);

        return str_replace(array_keys($this->parameters), $parameters, $message);
    }
}
