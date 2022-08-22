<div align="center">

# getwarp/criteria

Define rules to filter datasets in declarative way

[GitHub][link-github] •
[Packagist][link-packagist] •
[Installation](#installation) •
[Usage](#usage)

</div>

## Installation

Via Composer

```bash
$ composer require getwarp/criteria
```

## Usage

```php
use Warp\Criteria\Criteria;
use Warp\Criteria\Expression\ExpressionFactory;

$criteria = Criteria::new();
$ef = ExpressionFactory::new();

$criteria->where($ef->property('name', $ef->equals('Ben')))
    ->orderBy(['lastName' => SORT_ASC])
    ->offset(50)
    ->limit(25);
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

[link-github]: https://github.com/getwarp/criteria
[link-packagist]: https://packagist.org/packages/getwarp/criteria
[link-author]: https://github.com/hustlahusky
[link-contributors]: ../../contributors
[link-monorepo]: https://github.com/getwarp/warp
[link-issues]: https://github.com/getwarp/warp/issues
[link-pulls]: https://github.com/getwarp/warp/pulls
[link-contributing]: https://github.com/getwarp/warp/blob/3.1.x/CONTRIBUTING.md
[link-code-of-conduct]: https://github.com/getwarp/.github/blob/main/CODE_OF_CONDUCT.md
