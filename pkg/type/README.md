<div align="center">

# getwarp/type

Runtime type checking and casting in PHP

[GitHub][link-github] •
[Packagist][link-packagist] •
[Installation](#installation) •
[Usage](#usage)

</div>

## Installation

Via Composer

```bash
$ composer require getwarp/type
```

## Usage

```php
use Warp\Type\BuiltinType;

$int = BuiltinType::int();
\assert(true === $int->check(1));
\assert(false === $int->check('string'));
```

You can also use factory to create type object from string

```php
use Warp\Type\Factory\TypeFactoryAggregate;
use Warp\Type\Factory\MemoizedTypeFactory;

$factory = new MemoizedTypeFactory(TypeFactoryAggregate::default());
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

[Report issues][link-issues] and [send pull requests][link-pulls] in the [main Warp repository][link-monorepo]. Please
see [contributing guide][link-contributing] and [code of conduct][link-code-of-conduct] for details.

## Credits

- [Constantine Karnaukhov][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [license file](LICENSE.md) for more information.

[link-github]: https://github.com/getwarp/type
[link-packagist]: https://packagist.org/packages/getwarp/type
[link-author]: https://github.com/hustlahusky
[link-contributors]: ../../contributors
[link-monorepo]: https://github.com/getwarp/warp
[link-issues]: https://github.com/getwarp/warp/issues
[link-pulls]: https://github.com/getwarp/warp/pulls
[link-contributing]: https://github.com/getwarp/warp/blob/3.1.x/CONTRIBUTING.md
[link-code-of-conduct]: https://github.com/getwarp/.github/blob/main/CODE_OF_CONDUCT.md
