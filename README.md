# spaceonfire

Meta repository for composer libraries under [`spaceonfire/` vendor](https://packagist.org/packages/spaceonfire/).

## Getting Started

Requirements:

- Git
- Docker
- Node.js & NPM to work with [meta](https://github.com/mateodelnorte/meta)

Clone repository using git

```bash
$ git clone git@github.com:spaceonfire/spaceonfire.git && cd spaceonfire
```

Install `meta` utility with `npm`

```bash
$ npm ci
```

Initialize repositories with `meta`

```bash
$ npx meta git update
```

Then start docker container with php environment and install composer dependencies

```bash
$ make start
$ composer install --prefer-lowest
$ source bin/vendor-to-path.sh
```

## Usage

Update dependencies using --prefer-lowest and install suggestions by from our libraries:

```bash
$ bash bin/composer-update.sh
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.

## Security

If you discover any security related issues, please email genteelknight@gmail.com instead of using the issue tracker.

## Credits

- [Constantine Karnaukhov][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/spaceonfire/spaceonfire.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/spaceonfire/spaceonfire.svg?style=flat-square
[link-packagist]: https://packagist.org/packages/spaceonfire/spaceonfire
[link-downloads]: https://packagist.org/packages/spaceonfire/spaceonfire
[link-author]: https://github.com/hustlahusky
[link-contributors]: ../../contributors
