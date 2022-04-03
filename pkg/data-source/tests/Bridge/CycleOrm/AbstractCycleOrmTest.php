<?php

declare(strict_types=1);

namespace Warp\DataSource\Bridge\CycleOrm;

use BadMethodCallException;
use Cycle\ORM\ORM;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Warp\DataSource\RepositoryInterface;
use Spiral\Database\DatabaseManager;
use Spiral\Database\Driver\Driver;
use Spiral\Database\Driver\DriverInterface;

/**
 * @method static RepositoryInterface getRepository(string $className)
 * @method static ORM getOrm()
 * @method static DatabaseManager getDbal()
 * @method static DriverInterface|Driver getDriver()
 * @method static LoggerInterface getLogger()
 */
abstract class AbstractCycleOrmTest extends TestCase
{
    /**
     * @var CycleOrmTestCompanion
     */
    private static $companion;

    protected function setUp(): void
    {
        if (self::$companion === null) {
            self::$companion = new CycleOrmTestCompanion();
        }

        self::$companion->initialize();
    }

    public static function tearDownAfterClass(): void
    {
        self::$companion->tearDown();
    }

    public static function __callStatic(string $magicMethodName, array $arguments = [])
    {
        if (method_exists(self::$companion, $magicMethodName)) {
            return call_user_func_array([self::$companion, $magicMethodName], $arguments);
        }

        throw new BadMethodCallException(
            'Call to an undefined method ' . static::class . '::' . $magicMethodName . '()'
        );
    }
}
