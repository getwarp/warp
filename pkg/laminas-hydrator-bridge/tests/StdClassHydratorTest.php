<?php

declare(strict_types=1);

namespace Warp\Bridge\LaminasHydrator;

use Laminas\Hydrator\NamingStrategy\UnderscoreNamingStrategy;
use PHPUnit\Framework\TestCase;

class StdClassHydratorTest extends TestCase
{
    public function testDefault(): void
    {
        $hydrator = new StdClassHydrator();

        $array = [
            'id' => 30,
            'firstName' => 'Stephen',
        ];

        $hydrator->hydrate($array, $object = (object)[]);

        self::assertSame($array, $hydrator->extract($object));
    }

    public function testWithFilter(): void
    {
        $hydrator = new StdClassHydrator();
        $hydrator->addFilter('firstName', fn ($fieldName) => $fieldName !== 'firstName');

        $object = (object)[];
        $object->id = 30;
        $object->firstName = 'Stephen';

        self::assertSame([
            'id' => 30,
        ], $hydrator->extract($object));
    }

    public function testWithNamingStrategy(): void
    {
        $hydrator = new StdClassHydrator();
        $hydrator->setNamingStrategy(new UnderscoreNamingStrategy());

        $object = (object)[];
        $object->id = 30;
        $object->firstName = 'Stephen';

        self::assertSame([
            'id' => 30,
            'first_name' => 'Stephen',
        ], $hydrator->extract($object));
    }
}
