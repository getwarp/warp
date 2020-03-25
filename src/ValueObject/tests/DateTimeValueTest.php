<?php

declare(strict_types=1);

namespace spaceonfire\ValueObject;

use DateTime;
use DateTimeZone;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use spaceonfire\ValueObject\Date\DateException;
use spaceonfire\ValueObject\Date\DateTimeValue;

class DateTimeValueTest extends TestCase
{
    private static $oldTZ;

    public static function setUpBeforeClass(): void
    {
        self::$oldTZ = date_default_timezone_get();
        date_default_timezone_set('UTC');
    }

    public static function tearDownAfterClass(): void
    {
        date_default_timezone_set(self::$oldTZ);
    }

    public function testConstructor()
    {
        $this->assertEquals((new DateTime())->getTimestamp(), (new DateTimeValue())->getTimestamp());
        $this->assertEquals(
            (new DateTime('2000-01-01'))->getTimestamp(),
            (new DateTimeValue('2000-01-01'))->getTimestamp()
        );
    }

    public function testConstructorException()
    {
        $this->expectException(DateException::class);
        new DateTimeValue('19/10/2016 14:48:21');
    }

    public function testJson()
    {
        $this->assertEquals('"1978-01-23T10:40:00+00:00"', json_encode(DateTimeValue::from(254400000)));
    }

    public function testCreateFromFormat()
    {
        $this->assertInstanceOf(
            DateTimeValue::class,
            DateTimeValue::createFromFormat('Y-m-d H:i:s', '2050-08-13 10:40:00')
        );

        $this->assertEquals(
            '2050-08-13 10:40:00.123450',
            DateTimeValue::createFromFormat('Y-m-d H:i:s.u', '2050-08-13 10:40:00.12345')
                ->format('Y-m-d H:i:s.u')
        );

        $this->assertNull(DateTimeValue::createFromFormat('Y-m-d', '2014-10'));
    }

    public function testCreateFromFormatTimezone()
    {
        $this->assertInstanceOf(
            DateTimeValue::class,
            DateTimeValue::createFromFormat(
                'Y-m-d H:i:s',
                '2050-08-13 10:40:00',
                new DateTimeZone('UTC')
            )
        );

        $this->assertEquals(
            'UTC',
            DateTimeValue::createFromFormat('Y', '2050')->getTimezone()->getName()
        );
        $this->assertEquals(
            'Europe/Moscow',
            DateTimeValue::createFromFormat('Y', '2050', 'Europe/Moscow')->getTimezone()->getName()
        );
    }

    public function testCreateFromFormatTimezoneException()
    {
        $this->expectException(InvalidArgumentException::class);
        DateTimeValue::createFromFormat('Y-m-d H:i:s', '2050-08-13 10:40:00', 5);
    }

    public function testFrom()
    {
        $this->assertEquals(
            '1978-01-23 10:40:00',
            (string)DateTimeValue::from(254400000)
        );
        $this->assertEquals(
            '1978-01-23 10:40:00',
            (string)(new DateTimeValue())->setTimestamp(254400000)
        );
        $this->assertEquals(
            254400000,
            DateTimeValue::from(254400000)->getTimestamp()
        );

        // 64 bit
        $this->assertEquals(
            '2050-08-13 10:40:00',
            (string)DateTimeValue::from(2544000000)
        );
        $this->assertEquals(
            '2050-08-13 10:40:00',
            (string)(new DateTimeValue())->setTimestamp(2544000000)
        );
        $this->assertSame(
            is_int(2544000000) ? 2544000000 : '2544000000',
            DateTimeValue::from(2544000000)->getTimestamp()
        );

        $this->assertEquals(
            '1978-05-05 00:00:00',
            (string)DateTimeValue::from('1978-05-05')
        );

        $this->assertEquals(
            (new DateTime())->format('Y-m-d H:i:s'),
            (string)DateTimeValue::from(null)
        );

        $this->assertEquals(
            (new DateTime())->modify('+1 day')->format('Y-m-d H:i:s'),
            (string)DateTimeValue::from(86400)
        );

        $this->assertInstanceOf(
            DateTimeValue::class,
            DateTimeValue::from(new DateTime('1978-05-05'))
        );

        $this->assertEquals(
            '1978-05-05 12:00:00.123450',
            DateTimeValue::from(new DateTimeValue('1978-05-05 12:00:00.12345'))->format('Y-m-d H:i:s.u')
        );
    }

    public function testFromParts()
    {
        $this->assertEquals(
            '0001-12-09 00:00:00.000000',
            DateTimeValue::fromParts(1, 12, 9)->format('Y-m-d H:i:s.u')
        );
        $this->assertEquals(
            '0085-12-09 00:00:00.000000',
            DateTimeValue::fromParts(85, 12, 9)->format('Y-m-d H:i:s.u')
        );
        $this->assertEquals(
            '1985-01-01 00:00:00.000000',
            DateTimeValue::fromParts(1985, 1, 1)->format('Y-m-d H:i:s.u')
        );
        $this->assertEquals(
            '1985-12-19 00:00:00.000000',
            DateTimeValue::fromParts(1985, 12, 19)->format('Y-m-d H:i:s.u')
        );
        $this->assertEquals(
            '1985-12-09 01:02:00.000000',
            DateTimeValue::fromParts(1985, 12, 9, 1, 2)->format('Y-m-d H:i:s.u')
        );
        $this->assertEquals(
            '1985-12-09 01:02:03.000000',
            DateTimeValue::fromParts(1985, 12, 9, 1, 2, 3)->format('Y-m-d H:i:s.u')
        );
        $this->assertEquals(
            '1985-12-09 11:22:33.000000',
            DateTimeValue::fromParts(1985, 12, 9, 11, 22, 33)->format('Y-m-d H:i:s.u')
        );
        $this->assertEquals(
            '1985-12-09 11:22:59.123000',
            DateTimeValue::fromParts(1985, 12, 9, 11, 22, 59.123)->format('Y-m-d H:i:s.u')
        );
    }

    public function testFromPartsExceptions()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid date '1985-02-29 00:00:0.00000'");
        DateTimeValue::fromParts(1985, 2, 29);
    }
}
