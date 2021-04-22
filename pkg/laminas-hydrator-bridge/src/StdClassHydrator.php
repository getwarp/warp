<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\LaminasHydrator;

use Laminas\Hydrator\AbstractHydrator;

final class StdClassHydrator extends AbstractHydrator
{
    public function extract(object $object): array
    {
        $data = \get_object_vars($object);
        $filter = $this->getFilter();

        /**
         * @var string $name
         * @var mixed $value
         */
        foreach ($data as $name => $value) {
            // Filter keys, removing any we don't want
            if (!$filter->filter($name)) {
                unset($data[$name]);
                continue;
            }

            // Replace name if extracted differ
            $extracted = $this->extractName($name, $object);

            if ($extracted !== $name) {
                unset($data[$name]);
                $name = $extracted;
            }

            $data[$name] = $this->extractValue($name, $value, $object);
        }

        return $data;
    }

    public function hydrate(array $data, object $object): object
    {
        foreach ($data as $name => $value) {
            $property = $this->hydrateName($name, $data);
            $object->{$property} = $this->hydrateValue($property, $value, $data);
        }

        return $object;
    }
}
