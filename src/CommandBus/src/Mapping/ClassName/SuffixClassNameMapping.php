<?php

declare(strict_types=1);

namespace spaceonfire\CommandBus\Mapping\ClassName;

class SuffixClassNameMapping implements ClassNameMappingInterface
{
    /**
     * @var string
     */
    private $suffix;

    /**
     * SuffixClassNameMapping constructor.
     * @param string $suffix
     */
    public function __construct(string $suffix)
    {
        $this->suffix = $suffix;
    }

    /**
     * @inheritDoc
     */
    public function getClassName(string $commandClassName): string
    {
        return $commandClassName . $this->suffix;
    }
}
