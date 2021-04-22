<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Mapper;

use Cycle\ORM\ORMInterface;
use spaceonfire\Bridge\LaminasHydrator\StdClassHydrator;

class StdClassMapper extends HydratorMapper
{
    public function __construct(ORMInterface $orm, string $role, ?MapperPluginInterface $plugin = null)
    {
        parent::__construct($orm, $role, new StdClassHydrator(), $plugin);

        $this->entity = \stdClass::class;
    }

    /**
     * @param array<string,mixed> $data
     * @return array{object,array<string,mixed>}
     */
    public function init(array $data): array
    {
        return [new \stdClass(), $data];
    }
}
