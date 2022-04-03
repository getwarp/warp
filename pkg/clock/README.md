# Clock by spaceonfire

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Total Downloads][ico-downloads]][link-packagist]
![Code Coverage][ico-coverage]

This component provides enhanced **DateTime** classes and **Clock** API implementations in PHP.

## Install

Via Composer

```bash
$ composer require spaceonfire/clock
```

## Usage

DateTime:

```php
use spaceonfire\Clock\DateTimeImmutableValue;

$time = DateTimeImmutableValue::now();
// $time = DateTimeImmutableValue::from('2020-02-02 20:22:02');
// $time = DateTimeImmutableValue::from(3 * DateTimeImmutableValue::HOUR); // now + 3 hours

\assert($time instanceof \DateTimeImmutable);

echo (string)$time;
// 2020-02-02 20:22:02
echo \json_encode($time);
// 2020-02-02T20:22:02+00:00
```

Clock:

```php
use spaceonfire\Clock\FrozenClock;
use spaceonfire\Clock\SystemClock;

$clock = new FrozenClock(SystemClock::fromUTC());
$startedAt = $clock->now();
\sleep(5);
$finishedAt = $clock->now();
\assert($startedAt === $finishedAt);

$clock->reset();
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

[Report issues][link-issues] and [send Pull Requests][link-pulls] in the [main spaceonfire repository][link-monorepo].
Please see [CONTRIBUTING][link-contributing] and [CODE_OF_CONDUCT][link-code-of-conduct] for details.

## Credits

- [Constantine Karnaukhov][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/spaceonfire/clock.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/spaceonfire/clock.svg?style=flat-square
[ico-coverage]: https://img.shields.io/endpoint?style=flat-square&url=https%3A%2F%2Fgist.githubusercontent.com%2Fhustlahusky%2Fd62607c1a2e4707959b0142e0ea876cd%2Fraw%2Fclock.json
[link-packagist]: https://packagist.org/packages/spaceonfire/clock
[link-author]: https://github.com/hustlahusky
[link-contributors]: ../../contributors
[link-monorepo]: https://github.com/spaceonfire/spaceonfire
[link-issues]: https://github.com/spaceonfire/spaceonfire/issues
[link-pulls]: https://github.com/spaceonfire/spaceonfire/pulls
[link-contributing]: https://github.com/spaceonfire/spaceonfire/blob/master/CONTRIBUTING.md
[link-code-of-conduct]: https://github.com/spaceonfire/spaceonfire/blob/master/CODE_OF_CONDUCT.md
