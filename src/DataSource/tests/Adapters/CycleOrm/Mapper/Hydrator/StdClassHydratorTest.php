<?php

declare(strict_types=1);

namespace spaceonfire\DataSource\Adapters\CycleOrm\Mapper\Hydrator;

use PHPUnit\Framework\TestCase;
use stdClass;

class StdClassHydratorTest extends TestCase
{
    private $hydrator;

    protected function setUp(): void
    {
        $this->hydrator = new StdClassHydrator();
    }

    public function test(): void
    {
        $array = [
            'id' => 1,
            'name' => 'Stephen',
        ];

        $this->hydrator->hydrate($array, $object = new stdClass());

        self::assertEquals($array, $this->hydrator->extract($object));
    }
}
