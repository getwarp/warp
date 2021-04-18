<?php

declare(strict_types=1);

namespace spaceonfire\CommandBus\Mapping\ClassName;

use Webmozart\Assert\Assert;

class ReplacementClassNameMapping implements ClassNameMappingInterface
{
    /**
     * @var string|string[]
     */
    private $search;
    /**
     * @var string|string[]
     */
    private $replace;

    /**
     * ReplacementClassNameMapping constructor.
     * @param string|string[] $search
     * @param string|string[]|null $replace
     */
    public function __construct($search, $replace = null)
    {
        if ($replace === null && is_array($search)) {
            $replace = array_values($search);
            $search = array_keys($search);
        }

        $typeAssert = static function ($v, $message = ''): void {
            $method = is_array($v) ? 'allString' : 'string';
            Assert::$method($v, $message);
        };

        $typeAssert($search);
        $typeAssert($replace);

        $this->search = $search;
        $this->replace = $replace;
    }

    /**
     * @inheritDoc
     */
    public function getClassName(string $commandClassName): string
    {
        return str_replace($this->search, $this->replace, $commandClassName);
    }
}
