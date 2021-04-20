# Type by spaceonfire

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Total Downloads][ico-downloads]][link-packagist]
![Code Coverage][ico-coverage]

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

You can also use factory to create type object from string

```php
use spaceonfire\Type\Factory\CompositeTypeFactory;
use spaceonfire\Type\Factory\MemoizedTypeFactory;

$factory = new MemoizedTypeFactory(CompositeTypeFactory::makeWithDefaultFactories());
$factory->make('int');
$factory->make('string[]');
$factory->make('array<string,object>');
$factory->make('int|null');
$factory->make('Traversable|iterable|null');
$factory->make('Traversable&JsonSerializable');
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

[Report issues][link-issues] and [send Pull Requests][link-pulls] in the [main spaceonfire repository][link-monorepo].
Please see [CONTRIBUTING][link-contributing] and [CODE_OF_CONDUCT][link-code-of-conduct] for details.

## Credits

-   [Constantine Karnaukhov][link-author]
-   [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/spaceonfire/type.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/spaceonfire/type.svg?style=flat-square
[ico-coverage]: https://img.shields.io/endpoint?style=flat-square&url=https%3A%2F%2Fgist.githubusercontent.com%2Fhustlahusky%2Fd62607c1a2e4707959b0142e0ea876cd%2Fraw%2Ftype.json
[link-packagist]: https://packagist.org/packages/spaceonfire/type
[link-author]: https://github.com/hustlahusky
[link-contributors]: ../../contributors
[link-monorepo]: https://github.com/spaceonfire/spaceonfire
[link-issues]: https://github.com/spaceonfire/spaceonfire/issues
[link-pulls]: https://github.com/spaceonfire/spaceonfire/pulls
[link-contributing]: https://github.com/spaceonfire/spaceonfire/blob/master/CONTRIBUTING.md
[link-code-of-conduct]: https://github.com/spaceonfire/spaceonfire/blob/master/CODE_OF_CONDUCT.md
