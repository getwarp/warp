<?php

declare(strict_types=1);

namespace spaceonfire\DevTool\Shared;

abstract class AbstractConfig implements \JsonSerializable
{
    /**
     * @var array<string,mixed>
     */
    protected array $source;

    /**
     * @var array<string,mixed>
     */
    protected array $data = [];

    /**
     * @param array<string,mixed> $source
     */
    protected function __construct(array $source = [])
    {
        $this->source = [];

        foreach ($this->getSectionNames() as $sectionName) {
            if (isset($source[$sectionName]) || \array_key_exists($sectionName, $source)) {
                $this->source[$sectionName] = $source[$sectionName];
            }
        }
    }

    /**
     * @param string $section
     * @param mixed $default
     * @return mixed
     */
    public function getSection(string $section, $default = null)
    {
        return $this->data[$section] ?? $this->source[$section] ?? $default;
    }

    public function hasSection(string $section): bool
    {
        return isset($this->data[$section])
            || isset($this->source[$section])
            || \array_key_exists($section, $this->data)
            || \array_key_exists($section, $this->source);
    }

    /**
     * @param string $section
     * @param mixed $value
     */
    public function setSection(string $section, $value = null): void
    {
        $this->data[$section] = $value;
    }

    /**
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        $output = $this->source;

        foreach ($this->getSectionNames() as $section) {
            if (!$this->hasSection($section)) {
                continue;
            }

            $output[$section] = $this->getSection($section);
        }

        return $output;
    }

    /**
     * @inheritDoc
     * @return array<string,mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * @return string[]
     */
    final protected function getSectionNames(): array
    {
        static $cache = [];

        if (!isset($cache[static::class])) {
            $cache[static::class] = [];

            foreach ((new \ReflectionClass($this))->getReflectionConstants() as $const) {
                if (!$const->isPublic()) {
                    continue;
                }

                $v = $const->getValue();

                if (!\is_string($v)) {
                    throw new \RuntimeException('Non string constant.');
                }

                $cache[static::class][] = $v;
            }
        }

        return $cache[static::class];
    }
}
