<?php

declare(strict_types=1);

namespace spaceonfire\DataSource\Adapters\CycleOrm\Mapper;

use Cycle\ORM\ORMInterface;
use Laminas\Hydrator\Strategy\ClosureStrategy;
use Nette\Utils\Strings;
use spaceonfire\DataSource\Adapters\CycleOrm\AbstractCycleOrmTest;

class CycleMapperTest extends AbstractCycleOrmTest
{
    /**
     * @var BasicCycleMapper
     */
    private static $mapper;

    protected function setUp(): void
    {
        parent::setUp();

        if (self::$mapper === null) {
            self::$mapper = new class(self::getOrm(), 'user') extends BasicCycleMapper {
                public function __construct(ORMInterface $orm, string $role)
                {
                    parent::__construct($orm, $role);
                    $this->hydrator->addStrategy(
                        'name',
                        new ClosureStrategy(
                            static function ($value) {
                                return Strings::upper((string)$value);
                            },
                            static function ($value) {
                                return Strings::lower((string)$value);
                            }
                        )
                    );
                }
            };
        }
    }

    public function testConvertToStorage(): void
    {
        $storageVal = self::$mapper->convertToStorage('name', 'Admin User');
        self::assertEquals('ADMIN USER', $storageVal);
    }

    public function testConvertToDomain(): void
    {
        $domainVal = self::$mapper->convertToDomain('name', 'Admin User');
        self::assertEquals('admin user', $domainVal);
    }
}
