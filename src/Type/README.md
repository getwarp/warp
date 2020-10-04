# Type

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Total Downloads][ico-downloads]][link-downloads]
[![Code Coverage][ico-coverage]][link-actions]
[![Build Status][ico-build-status]][link-actions]

Collection of objects that provides ability of checking value types.

## Install

Via Composer

```bash
$ composer require spaceonfire/type
```

## Usage

```php
use spaceonfire\Type\BuiltinType;
use Webmozart\Assert\Assert;

$int = new BuiltinType(BuiltinType::INT);
Assert::true($int->check(1));
Assert::false($int->check('1'));

$intNonStrict = new BuiltinType(BuiltinType::INT, false);
Assert::true($intNonStrict->check('1'));
Assert::same(1, $intNonStrict->cast('1'));
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

```bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.

## Security

If you discover any security related issues, please email genteelknight@gmail.com instead of using the issue tracker.

## Credits

- [Constantine Karnaukhov][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/spaceonfire/type.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/spaceonfire/type.svg?style=flat-square
[ico-coverage]: https://img.shields.io/endpoint?style=flat-square&url=https%3A%2F%2Fgist.githubusercontent.com%2Fhustlahusky%2Fd62607c1a2e4707959b0142e0ea876cd%2Fraw%2Fspaceonfire-type.json
[ico-build-status]: https://github.com/spaceonfire/type/workflows/Build%20Pipeline/badge.svg
[link-packagist]: https://packagist.org/packages/spaceonfire/type
[link-downloads]: https://packagist.org/packages/spaceonfire/type
[link-author]: https://github.com/hustlahusky
[link-contributors]: ../../contributors
[link-actions]: ../../actions
