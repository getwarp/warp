<?php

declare(strict_types=1);

namespace spaceonfire\ValueObject\Date;

use PHPUnit\Framework\TestCase;

class DateTimeValueTest extends TestCase
{
    private static ?string $oldTZ = null;

    public static function setUpBeforeClass(): void
    {
        self::$oldTZ = \date_default_timezone_get();
        \date_default_timezone_set('UTC');
    }

    public static function tearDownAfterClass(): void
    {
        \date_default_timezone_set(self::$oldTZ);
    }

    public function testConstructor(): void
    {
        self::assertSame((new \DateTime())->getTimestamp(), (new DateTimeValue())->getTimestamp());
        self::assertSame(
            (new \DateTime('2000-01-01'))->getTimestamp(),
            (new DateTimeValue('2000-01-01'))->getTimestamp()
        );
    }

    public function testConstructorException(): void
    {
        $this->expectException(DateException::class);
        new DateTimeValue('19/10/2016 14:48:21');
    }

    /**
     * @see https://github.com/nette/utils/blob/master/tests/Utils/DateTime.JSON.phpt
     */
    public function testJson(): void
    {
        self::assertSame('"1978-01-23T10:40:00+00:00"', \json_encode(DateTimeValue::from(254_400_000), JSON_THROW_ON_ERROR));
    }

    /**
     * @see https://github.com/nette/utils/blob/master/tests/Utils/DateTime.createFromFormat.phpt
     */
    public function testCreateFromFormat(): void
    {
        self::assertInstanceOf(
            DateTimeValue::class,
            DateTimeValue::createFromFormat('Y-m-d H:i:s', '2050-08-13 10:40:00')
        );

        self::assertSame(
            '2050-08-13 10:40:00.123450',
            DateTimeValue::createFromFormat('Y-m-d H:i:s.u', '2050-08-13 10:40:00.12345')
                ->format('Y-m-d H:i:s.u')
        );

        self::assertNull(DateTimeValue::createFromFormat('Y-m-d', '2014-10'));
    }

    /**
     * @see https://github.com/nette/utils/blob/master/tests/Utils/DateTime.createFromFormat.phpt
     */
    public function testCreateFromFormatTimezone(): void
    {
        self::assertInstanceOf(
            DateTimeValue::class,
            DateTimeValue::createFromFormat('Y-m-d H:i:s', '2050-08-13 10:40:00', new \DateTimeZone('UTC'))
        );

        self::assertSame('UTC', DateTimeValue::createFromFormat('Y', '2050')->getTimezone()->getName());
        self::assertSame(
            'Europe/Moscow',
            DateTimeValue::createFromFormat('Y', '2050', new \DateTimeZone('Europe/Moscow'))->getTimezone()->getName()
        );
    }

    /**
     * @see https://github.com/nette/utils/blob/master/tests/Utils/DateTime.from.phpt
     */
    public function testFrom(): void
    {
        self::assertSame('1978-01-23 10:40:00', (string)DateTimeValue::from(254_400_000));
        self::assertSame('1978-01-23 10:40:00', (string)(new DateTimeValue())->setTimestamp(254_400_000));
        self::assertSame(254_400_000, DateTimeValue::from(254_400_000)->getTimestamp());

        // 64 bit
        self::assertSame('2050-08-13 10:40:00', (string)DateTimeValue::from(2_544_000_000));
        self::assertSame('2050-08-13 10:40:00', (string)(new DateTimeValue())->setTimestamp(2_544_000_000));
        self::assertSame(
            is_int(2_544_000_000) ? 2_544_000_000 : '2544000000',
            DateTimeValue::from(2_544_000_000)->getTimestamp()
        );

        self::assertSame('1978-05-05 00:00:00', (string)DateTimeValue::from('1978-05-05'));

        self::assertSame((new \DateTime())->format('Y-m-d H:i:s'), (string)DateTimeValue::from(null));

        self::assertSame(
            (new \DateTime())->modify('+1 day')->format('Y-m-d H:i:s'),
            (string)DateTimeValue::from(86400)
        );

        self::assertInstanceOf(DateTimeValue::class, DateTimeValue::from(new \DateTime('1978-05-05')));

        self::assertSame(
            '1978-05-05 12:00:00.123450',
            DateTimeValue::from(new DateTimeValue('1978-05-05 12:00:00.12345'))->format('Y-m-d H:i:s.u')
        );

        $now = DateTimeValue::from('now');
        self::assertSame($now, DateTimeValue::from($now));
    }

    /**
     * @see https://github.com/nette/utils/blob/master/tests/Utils/DateTime.fromParts.phpt
     */
    public function testFromParts(): void
    {
        self::assertSame(
            '0001-12-09 00:00:00.000000',
            DateTimeValue::fromParts(1, 12, 9)->format('Y-m-d H:i:s.u')
        );
        self::assertSame(
            '0085-12-09 00:00:00.000000',
            DateTimeValue::fromParts(85, 12, 9)->format('Y-m-d H:i:s.u')
        );
        self::assertSame(
            '1985-01-01 00:00:00.000000',
            DateTimeValue::fromParts(1985, 1, 1)->format('Y-m-d H:i:s.u')
        );
        self::assertSame(
            '1985-12-19 00:00:00.000000',
            DateTimeValue::fromParts(1985, 12, 19)->format('Y-m-d H:i:s.u')
        );
        self::assertSame(
            '1985-12-09 01:02:00.000000',
            DateTimeValue::fromParts(1985, 12, 9, 1, 2)->format('Y-m-d H:i:s.u')
        );
        self::assertSame(
            '1985-12-09 01:02:03.000000',
            DateTimeValue::fromParts(1985, 12, 9, 1, 2, 3)->format('Y-m-d H:i:s.u')
        );
        self::assertSame(
            '1985-12-09 11:22:33.000000',
            DateTimeValue::fromParts(1985, 12, 9, 11, 22, 33)->format('Y-m-d H:i:s.u')
        );
        self::assertSame(
            '1985-12-09 11:22:59.123000',
            DateTimeValue::fromParts(1985, 12, 9, 11, 22, 59.123)->format('Y-m-d H:i:s.u')
        );
    }

    /**
     * @see https://github.com/nette/utils/blob/master/tests/Utils/DateTime.fromParts.phpt
     */
    public function testFromPartsExceptions(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid date: "1985-02-29 00:00:0.00000".');
        DateTimeValue::fromParts(1985, 2, 29);
    }
}
