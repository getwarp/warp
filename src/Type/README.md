# type

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Total Downloads][ico-downloads]][link-downloads]

Collection of value objects provides ability of type checking in runtime.

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

$intNoString = new BuiltinType(BuiltinType::INT, false);
Assert::true($int->check('1'));
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
[link-packagist]: https://packagist.org/packages/spaceonfire/type
[link-downloads]: https://packagist.org/packages/spaceonfire/type
[link-author]: https://github.com/hustlahusky
[link-contributors]: ../../contributors
