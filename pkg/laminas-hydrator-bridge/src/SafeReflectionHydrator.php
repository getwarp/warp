<?php

declare(strict_types=1);

namespace Warp\Bridge\LaminasHydrator;

use Laminas\Hydrator\AbstractHydrator;
use Laminas\Hydrator\ReflectionHydrator;

/**
 * Acts like default {@see ReflectionHydrator}, but skips not initialized properties on extracting.
 */
final class SafeReflectionHydrator extends AbstractHydrator
{
    /**
     * @var array<class-string,array<string,\ReflectionProperty>>
     */
    private static $reflProperties = [];

    public function extract(object $object): array
    {
        $result = [];

        foreach (self::getReflProperties($object) as $property) {
            $propertyName = $this->extractName($property->getName(), $object);

            if (!$this->getCompositeFilter()->filter($propertyName)) {
                continue;
            }

            if (!$property->isInitialized($object)) {
                continue;
            }

            $value = $property->getValue($object);
            $result[$propertyName] = $this->extractValue($propertyName, $value, $object);
        }

        return $result;
    }

    public function hydrate(array $data, object $object)
    {
        $reflProperties = self::getReflProperties($object);

        foreach ($data as $key => $value) {
            $name = $this->hydrateName($key, $data);

            if (!isset($reflProperties[$name])) {
                continue;
            }

            $reflProperties[$name]->setValue($object, $this->hydrateValue($name, $value, $data));
        }

        return $object;
    }

    /**
     * @return array<string,\ReflectionProperty>
     */
    private static function getReflProperties(object $input): array
    {
        $class = \get_class($input);

        if (isset(self::$reflProperties[$class])) {
            return self::$reflProperties[$class];
        }

        self::$reflProperties[$class] = [];
        $reflClass = new \ReflectionClass($class);
        $reflProperties = $reflClass->getProperties(
            \ReflectionProperty::IS_PUBLIC | \ReflectionProperty::IS_PROTECTED | \ReflectionProperty::IS_PRIVATE
        );

        foreach ($reflProperties as $property) {
            $property->setAccessible(true);
            self::$reflProperties[$class][$property->getName()] = $property;
        }

        return self::$reflProperties[$class];
    }
}
