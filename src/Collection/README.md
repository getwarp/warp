# collection

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Total Downloads][ico-downloads]][link-downloads]

Framework agnostic PHP collections

## Structure

```
docs/         API documentation
src/          Source code
tests/        Unit tests
```

## Install

Via Composer

``` bash
$ composer require spaceonfire/collection
```

## Usage

``` php
$collection = new spaceonfire\Collection\Collection([0, 1, 2, 3]);
//$collection->sum() === 6
```

Read [documentation](./docs/README.md) for more.

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

``` bash
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

[ico-version]: https://img.shields.io/packagist/v/spaceonfire/collection.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/spaceonfire/collection.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/spaceonfire/collection
[link-downloads]: https://packagist.org/packages/spaceonfire/collection
[link-author]: https://github.com/hustlahusky
[link-contributors]: ../../contributors
