<div align="center">

# getwarp/clock

Enhanced **DateTime** classes and **Clock** API implementation

[GitHub][link-github] •
[Packagist][link-packagist] •
[Installation](#installation) •
[Usage](#usage)

</div>

## Installation

Via Composer

```bash
composer require getwarp/clock
```

## Usage

DateTime:

```php
use Warp\Clock\DateTimeImmutableValue;

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
use Warp\Clock\FrozenClock;
use Warp\Clock\SystemClock;

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

[Report issues][link-issues] and [send pull requests][link-pulls] in the [main Warp repository][link-monorepo]. Please
see [contributing guide][link-contributing] and [code of conduct][link-code-of-conduct] for details.

## Credits

- [Constantine Karnaukhov][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [license file](LICENSE.md) for more information.

[link-github]: https://github.com/getwarp/clock
[link-packagist]: https://packagist.org/packages/getwarp/clock
[link-author]: https://github.com/hustlahusky
[link-contributors]: ../../contributors
[link-monorepo]: https://github.com/getwarp/warp
[link-issues]: https://github.com/getwarp/warp/issues
[link-pulls]: https://github.com/getwarp/warp/pulls
[link-contributing]: https://github.com/getwarp/warp/blob/3.1.x/CONTRIBUTING.md
[link-code-of-conduct]: https://github.com/getwarp/.github/blob/main/CODE_OF_CONDUCT.md
